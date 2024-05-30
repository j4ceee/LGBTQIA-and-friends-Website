#!/bin/sh

printf "#-----Starting node_css_min.sh\n"

while inotifywait -e modify,create,delete /app/css; do
    # loop over each css file in the directory
    for file in /app/css/*.css; do
        # get the base name of the file (without extension)
        base_name=$(basename "$file" .css)
        # check if the file name already contains .min
        if echo "$base_name" | grep -qv ".min"; then
            # run your minification command with .min inserted between the file name and file type
            cleancss -o "/app/css/min/${base_name}.min.css" "$file"
        fi
    done
done