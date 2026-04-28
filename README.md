# Koro Challenge - Kubernetes Orchestration & High Availability

## Overview

This project demonstrates the deployment of a stateless PHP application with MySQL and Redis using Kubernetes.  
The solution focuses on:

- Container orchestration
- Horizontal scaling
- Load balancing using NGINX Ingress
- Basic stateful service setup (MySQL + Redis)
- High Availability design considerations

---

## Architecture

The system consists of:

- **PHP Application (3 replicas)**  
  Stateless service handling HTTP requests

- **MySQL Database**  
  Persistent storage layer using PVC

- **Redis Cache**  
  In-memory caching layer

- **NGINX Ingress Controller**  
  Routes external traffic to application pods

---

## Prerequisites

- Minikube (Docker driver)
- kubectl
- Docker

---

## Setup Instructions

### 1. Start Minikube

```bash
minikube start --driver=docker

```

### 2. Enable Ingress

```bash

minikube addons enable ingress

```
### 3. Deploy All Resources

```bash

kubectl apply -k k8s/

```
### 4. Verify Pods

```bash

kubectl get pods -n koro-app

```
### 5. Application Image

As the application image is built for platform arm64 and it was not compatible in my machine, so re-built the image locally and tested

 Replaced 
  
    image: ghcr.io/korohandelsgmbh/coding-test-2025:latest

with 

    image: php-app:local
    imagePullPolicy: Never

### 5. Access Application

```bash

kubectl port-forward svc/php-app 8080:80 -n koro-app

```
Then open:

http://localhost:8080

Environment Variables Used

The PHP application requires:

DB_HOST
DB_USER
DB_PASS
DB_NAME
REDIS_HOST
REDIS_PORT

Secrets are used for sensitive values like DB credentials.

### 6. Kubernetes Design Choices

- Stateless PHP pods (horizontal scaling)
- MySQL uses PersistentVolume for data storage
- Redis deployed as standalone cache service
- Ingress used for external routing
- Secrets used for DB credentials

### 7. Summary
- PHP layer is horizontally scalable
- MySQL and Redis are single-node in this demo setup
- Production design recommends:
- MySQL: RDS / Aurora or replication cluster
- Redis: Sentinel or managed Redis (ElastiCache)
- Multi-AZ deployment for both data layers