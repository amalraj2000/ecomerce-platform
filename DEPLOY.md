# Deployment Guide

This app deploys as a single Docker container to **Render**, backed by **TiDB Cloud** (MySQL-compatible, free 5 GB).

---

## Architecture

```
Browser
  └── Render Web Service (Docker: Nginx + PHP-FPM + Laravel + Inertia/Vue)
            └── TiDB Cloud (MySQL-compatible, free serverless)
```

---

## Step 1: Set Up TiDB Cloud Database

1. Go to [tidbcloud.com](https://tidbcloud.com) → Sign up (free)
2. Create a **Serverless** cluster (free tier, 5 GB)
3. Once running, click **Connect** → choose **General** → note these values:
   - `HOST` (e.g. `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`)
   - `PORT` (usually `4000`)
   - `USERNAME` (e.g. `2abc123def.root`)
   - `PASSWORD`
   - `DATABASE` (create one named `ecomerce_platform`)
4. Download the **CA certificate** from the Connect dialog (needed for SSL)

---

## Step 2: Push to GitHub

```bash
git add .
git commit -m "chore: add Docker deployment files"
git push origin main
```

---

## Step 3: Create a Render Web Service

1. Go to [render.com](https://render.com) → **New → Web Service**
2. Connect your GitHub repository
3. Configure the service:
   - **Runtime**: Docker
   - **Branch**: `main`
   - **Dockerfile path**: `./Dockerfile` (auto-detected)
   - **Plan**: Free (or Starter for no cold starts)

---

## Step 4: Set Environment Variables in Render

In your Render service dashboard → **Environment** tab, add these:

```env
APP_NAME="Ecomerce Platform"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...          # run: php artisan key:generate --show
APP_URL=https://your-service.onrender.com

DB_CONNECTION=mysql
DB_HOST=<TiDB host>
DB_PORT=4000
DB_DATABASE=ecomerce_platform
DB_USERNAME=<TiDB username>
DB_PASSWORD=<TiDB password>
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

LOG_CHANNEL=stderr
LOG_LEVEL=error
```

> **Generating APP_KEY locally:**
> ```bash
> php artisan key:generate --show
> ```

---

## Step 5: Deploy

1. Click **Deploy** in Render — it will build the Docker image and run migrations automatically
2. First deploy takes ~5–10 minutes (Docker build)
3. Subsequent deploys are faster (layer caching)

---

## Step 6: Update APP_URL

Once deployed, copy your `.onrender.com` URL and update the `APP_URL` environment variable in Render to match.

---

## Important Notes

| Topic | Detail |
|---|---|
| **Cold starts** | Free Render plan sleeps after 15 min. First request takes ~45s. Upgrade to Starter ($7/mo) to avoid. |
| **File storage** | The container's filesystem is ephemeral. Use `FILESYSTEM_DISK=s3` with S3/R2/Cloudflare for uploaded files. |
| **Queue workers** | For background jobs, add a separate Render **Background Worker** service pointing to `php artisan queue:work` |
| **TiDB SSL** | TiDB Cloud requires SSL. The `MYSQL_ATTR_SSL_CA` env var points to the system CA bundle in the container. |

---

## Local Docker Testing

Test the Docker build locally before pushing:

```bash
# Build the image
docker build -t ecomerce-platform .

# Create a test env file (copy .env and set DB to your TiDB creds)
cp .env .env.docker

# Run locally
docker run --rm -p 8080:80 --env-file .env.docker ecomerce-platform

# Visit http://localhost:8080
```
