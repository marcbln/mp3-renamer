#!/bin/bash
#
# rename mp3 files of audio book for right playing order in mp3 player
#
# 06/2022 created
#


# ---- args
SCRIPTNAME=$(basename $0)
if [ $# -ne 2 ]; then
    echo "$SCRIPTNAME - rename mp3 files of audio book for right playing order in mp3 player."
    echo "usage: $SCRIPTNAME path-to-audio-book/s path-to-destination-script"
    exit 77
fi

REALPATH_AUDIOBOOK=`readlink -f "$1"`
REALPATH_DEST_SCRIPT=`readlink -f "$2"`


function fix() {
    # recursive function

    readarray -d '' dirs < <(find "$1" -mindepth 1 -maxdepth 1 -type d -print0)
    readarray -d '' files < <(find "$1" -mindepth 1 -maxdepth 1 -type f -iname "*mp3" -print0)

    # ---- print info
#    echo ${#dirs[@]} dirs found in $1
#    echo ${#files[@]} files found in $1

    # ---- subdirs    
    if [ ${#dirs[@]} -gt 0 ]; then
        echo "#### SUBDIRS FOUND: ${#dirs[@]}"
        for d in "${dirs[@]}"
        do
            fix "$d"
        done
    fi

    # ---- mp3 files
    if [ ${#files[@]} -gt 0 ]
    then
        echo "#### FILES FOUND: ${#files[@]}"
        php fix-files.php "$1" "$REALPATH_DEST_SCRIPT"
    fi
}


# ---- main ----
echo "#!/bin/sh" > "$REALPATH_DEST_SCRIPT"
echo "# -------" >> "$REALPATH_DEST_SCRIPT"
fix "$REALPATH_AUDIOBOOK"
chmod +x $REALPATH_DEST_SCRIPT
