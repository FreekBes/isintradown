FROM php:7.4-fpm-alpine

COPY . .

ENTRYPOINT [ "sh" , "./entrypoint.sh" ]

# To start, run inside the root of this repo
# docker build -t is-intra-down .
# docker stop is-intra-down || true
# docker rm is-intra-down || true
# docker run -v $PWD/history.json:/root/history.json -d -p 4242:8080 --name is-intra-down is-intra-down
