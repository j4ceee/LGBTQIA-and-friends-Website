### Docker Setup ###

Create a `password.txt` file inside the `db` directory with the password for the database.
It should only be one line with no spaces or newlines.

Create a `cal` folder inside the `src` directory.


### Building and running your application

When you're ready, start your application by running:
`docker compose up --build`.

Or, if changes of the code should be reflected in the running container:
`docker compose watch`.

Your application will be available at http://localhost:9000.

### Stopping your application ###

To stop your application, run:
`docker compose down`.

Or, if you want to stop and remove all volumes, run:
`docker compose down -v`.

### Deploying your application to the cloud

First, build your image, e.g.: `docker build -t myapp .`.
If your cloud uses a different CPU architecture than your development
machine (e.g., you are on a Mac M1 and your cloud provider is amd64),
you'll want to build the image for that platform, e.g.:
`docker build --platform=linux/amd64 -t myapp .`.

Then, push it to your registry, e.g. `docker push myregistry.com/myapp`.

Consult Docker's [getting started](https://docs.docker.com/go/get-started-sharing/)
docs for more detail on building and pushing.