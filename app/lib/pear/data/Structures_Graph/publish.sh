#!/bin/sh
./package.sh
scp BUILD/*.tgz root@sergiocarvalho.com:/home/httpd/vhosts/com/sergiocarvalho/pear-base/pear
(cd docs; ./generate.sh)
scp -r docs/html/* root@iluvatar.portugalmail.pt:/home/httpd/vhosts/com/sergiocarvalho/pear-base/pear/docs/Structures_Graph
