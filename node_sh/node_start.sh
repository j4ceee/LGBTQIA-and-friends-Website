#!/bin/sh

printf "#-----Starting node_start.sh\n"

# execute node_sh/node_css_min.sh in the background
sh /app/node_sh/node_css_min.sh &

# execute node_sh/node_js_min.sh in the background
sh /app/node_sh/node_js_min.sh &

# keep the script running
tail -f /dev/null