# Is Intra down?
Very much a work in progress

[isintradown.fr](https://isintradown.fr)


## Running (in development mode)
Add your intra credentials to `./src/config.php`
Run `php -S localhost:8080` for the website
Run `env php src/check.php` for the history (saved to `history.json`)

## Docker
To start, run inside the root of this repo
Replace `$PWD/history.json` with the path you want to store the history on your local disk
```
docker build -t is-intra-down .
docker stop is-intra-down || true
docker rm is-intra-down || true
docker run -v $PWD/history.json:/root/history.json -d -p 8080:8080 --name is-intra-down is-intra-down
```

## Docker compose
```yaml
version: '3.3'
services:
  is-intra-down:
	build: ./is-intra-down
    volumes:
      - '$PWD/history.json:/root/history.json'
    ports:
      - '8080:8080'
```