# Deployment Guide (SiapPOS)

Since this application uses **SQLite** for its database (`storage/siappos.sqlite`), deploying it to stateless platforms like Vercel or Netlify is **not recommended** because the database file will be reset on every deployment, causing data loss.

**Recommended Solution: Docker + Persistent Volume**

The best free/cheap options for hosting SQLite PHP apps are:
1.  **Railway.app** (Paid/Trial)
2.  **Fly.io** (Free Tier available)
3.  **Hetzner / DigitalOcean VPS** ($5/mo)

---

## Option 1: Deploy to Railway (Easiest)

1.  Push this code to GitHub.
2.  Go to [Railway.app](https://railway.app/).
3.  Click "New Project" -> "Deploy from GitHub repo".
4.  Select your repository.
5.  Railway will detect the `Dockerfile` automatically.
6.  **Crucial Step:** Once deployed, go to the project **Settings** -> **Variables**.
7.  Go to **Volumes** tab.
8.  Add a volume mounted to `/var/www/html/storage`.
    -   This ensures your database is saved even if the app restarts.

---

## Option 2: Deploy to Fly.io (Command Line)

1.  Install `flyctl`: https://fly.io/docs/hands-on/install-flyctl/
2.  Login: `fly auth login`
3.  Initialize app inside the project folder:
    ```bash
    fly launch
    ```
    -   Select "No" for Postgres/Redis (we use SQLite).
4.  Create a persistent volume for SQLite:
    ```bash
    fly volumes create siappos_data --size 1
    ```
5.  Edit the generated `fly.toml` file. Add this block:
    ```toml
    [mounts]
      source = "siappos_data"
      destination = "/var/www/html/storage"
    ```
6.  Deploy:
    ```bash
    fly deploy
    ```

---

## Option 3: Local Docker

If you just want to run it on another machine:

```bash
docker-compose up -d --build
```
Access at: http://localhost:8088
