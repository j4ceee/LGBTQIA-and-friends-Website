#!/bin/sh

printf "#-----Starting node_css_min.sh\n"

while inotifywait -e modify,create,delete /app/css; do
    # loop over each css file in the directory
    for file in /app/css/*.css; do
        # get the base name of the file (without extension)
        base_name=$(basename "$file" .css)
        # check if the file name already contains .min
        if echo "$base_name" | grep -qv ".min"; then
            # run minification command with .min between the file name & type
            cleancss --with-rebase -o "/app/css/min/${base_name}.min.css" -O1 all:off "$file"
        fi
    done
done