<?php
use WhyooOs\Util\UtilDebug;
use WhyooOs\Util\UtilFilesystem;



function extractByRegex(array $files, string $regex)
{
    $ret = [];
    foreach($files as $idx => $file) {
#        UtilDebug::d($file);
        if(preg_match($regex, $file, $matches)) {
            $ret[] = $matches[1];
       } else {
            return null;
        }
    }

    $ret = array_map('intval', $ret);

    return $ret;
}

include "vendor/autoload.php";


function getRenames(string $dirname)
{
    $mp3Files = UtilFileSystem::findByExtensions($dirname, 'mp3', false, false);
    $aInts = extractByRegex($mp3Files, '/Teil (\d+)/');
    if(is_null($aInts)) {
        $aInts = extractByRegex($mp3Files, '/^(\d+)/');
    }
#    UtilDebug::d($mp3Files, $aInts);

    if(is_null($aInts)) {
        return null;
    }


    array_multisort($aInts, $mp3Files, SORT_ASC, SORT_NUMERIC);

    # $aZeroPadded = array_map(fn($x) => sprintf("%02d", $x), $aInts);
    $renames = [];
    foreach ($mp3Files as $idx => $filename) {
        $idx1 = $aInts[$idx];
        $regex = "/^0*$idx1(.*)/";
        $filenameNew = trim(preg_replace($regex, '' . '$1', $filename));
        // prepend
        $filenameNew = sprintf('%02d', $idx1) . ' ' . $filenameNew;
        # UtilDebug::d("---->", $regex, $filenameNew);
        $from = UtilFilesystem::joinPaths($dirname, $filename);
        $to = UtilFilesystem::joinPaths($dirname, $filenameNew);
        if($from != $to) {
            $renames[] = "mv '$from' '$to'";
        }
    }

    return $renames;
}




// ---- main ----
// ---- main ----
// ---- main ----
$PATH_AUDIOBOOK_DIR = $argv[1];
$PATH_DEST_SCRIPT = $argv[2];
$renames = getRenames($PATH_AUDIOBOOK_DIR);
if($renames) {
    file_put_contents($PATH_DEST_SCRIPT, implode("\n", $renames) . "\n", FILE_APPEND);
}




