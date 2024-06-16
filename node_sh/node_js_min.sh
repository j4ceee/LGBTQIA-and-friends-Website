#!/bin/sh

printf "#-----Starting node_js_min.sh\n"

while inotifywait -e modify,create,delete /app/js; do
    # loop over each js file in the directory
    for file in /app/js/*.js; do
        # get the base name of the file (without extension)
        base_name=$(basename "$file" .js)
        # check if the file name already contains .min
        if echo "$base_name" | grep -qv ".min"; then
            # run minification command with .min between the file name & type
            terser --compress --mangle -o "/app/js/min/${base_name}.min.js" "$file"
        fi
    done
done