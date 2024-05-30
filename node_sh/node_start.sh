#!/bin/sh

printf "#-----Starting node_start.sh\n"

npm install -g npm@10.8.1

npm install -g clean-css-cli

npm uninstall -g inflight

for file in /app/css/*.css; do
    # get the base name of the file (without extension)
    base_name=$(basename "$file" .css)
    # check if the file name already contains .min
    if echo "$base_name" | grep -qv ".min"; then
        # run your minification command with .min inserted between the file name and file type
        cleancss -o "/app/css/min/${base_name}.min.css" "$file"
    fi
done

# execute node_sh/node_css_min.sh
sh /app/node_sh/node_css_min.sh