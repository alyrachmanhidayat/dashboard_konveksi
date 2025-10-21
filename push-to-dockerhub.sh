#!/bin/bash

# Script to build and push the Docker image to Docker Hub

echo "Tagging the image..."
docker tag dashboard-konveksi-app:latest ryanaputra/dashboard-konveksi-prod:latest

echo "Pushing image to Docker Hub..."
docker push ryanaputra/dashboard-konveksi-prod:latest

echo "Image has been pushed to Docker Hub successfully!"
echo "You can now deploy on other servers using the docker-compose.production.yml file."