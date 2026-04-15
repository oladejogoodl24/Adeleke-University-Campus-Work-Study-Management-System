# GitHub Setup & Hosting Guide

## Step 1: Create a GitHub Repository

1. Go to [GitHub.com](https://github.com)
2. Log in to your account (or sign up if you don't have one)
3. Click the **+** icon in the top right → Select **New repository**
4. Fill in the details:
   - **Repository name**: `work-study-portal` (or your preferred name)
   - **Description**: "Adeleke University Work-Study Management System"
   - **Visibility**: Choose **Public** (recommended for portfolio) or **Private**
   - Click **Create repository**

## Step 2: Add GitHub Remote & Push Code

After creating the repository on GitHub, run these commands in your terminal:

```bash
cd c:\xampp\htdocs\work_study

# Add the GitHub repository as remote origin
git remote add origin https://github.com/YOUR_USERNAME/work-study-portal.git

# Rename the main branch to match GitHub's default (if using older git)
git branch -M main

# Push the code to GitHub
git push -u origin main
```

**Replace `YOUR_USERNAME` with your actual GitHub username**

## Step 3: Verify on GitHub

- Go to your repository on GitHub
- You should see all your project files uploaded
- The README.md will be displayed on the repository homepage

## Step 4: Future Updates

For future commits:

```bash
# Make changes to your files
git add .
git commit -m "Your descriptive commit message"
git push origin main
```

## GitHub Pages Deployment (Optional - for static sites)

Since this is a PHP application requiring a server backend, GitHub Pages alone won't work. Instead, consider:

### Option A: Use GitHub Codespaces
GitHub Codespaces allows you to run PHP directly in the cloud:
1. Go to your GitHub repository
2. Click **Code** → **Codespaces** → **Create codespace on main**
3. In the terminal: `php -S localhost:8000`
4. Access your app in the preview browser

### Option B: Deploy to Free Hosting Platforms

#### Heroku (Free tier limited)
```bash
# Install Heroku CLI
# Login and deploy
heroku login
heroku create your-app-name
git push heroku main
```

#### Railway.app / Render.com
- More generous free tiers
- Supports PHP with MySQL
- Easy MySQL database setup
- Simple GitHub integration

#### Replit
- Free PHP hosting
- Built-in MySQL support
- Easy to set up

#### 000webhost
- Free PHP hosting with MySQL
- Minimal setup required

### Step 5: Important Security Notes for Hosting

⚠️ **Before deploying to a live server:**

1. **Create Environment Variables**: Instead of hardcoding `db.php`:
   ```php
   $servername = getenv('DB_HOST') ?: 'localhost';
   $username = getenv('DB_USER') ?: 'root';
   $password = getenv('DB_PASS') ?: '';
   $database = getenv('DB_NAME') ?: 'adeleke_work_study';
   ```

2. **Update `.gitignore`** to ensure it includes:
   - `.env` (environment variables file)
   - `db.php` (credentials)
   - Sensitive configuration files

3. **Set Strong Admin Password**: Use the `hash.php` utility or:
   ```bash
   php -r "echo password_hash('your_strong_password', PASSWORD_DEFAULT);"
   ```

4. **Enable HTTPS**: Most hosting platforms auto-enable SSL

5. **Secure Session Cookies**: Add to all session-using files:
   ```php
   session_set_cookie_params([
       'secure' => true,
       'httponly' => true,
       'samesite' => 'Strict'
   ]);
   ```

## Troubleshooting

### "fatal: Not a git repository"
```bash
cd c:\xampp\htdocs\work_study
git init
```

### "Permission denied (publickey)"
You need to set up SSH keys:
```bash
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
# Add the public key to GitHub Settings → SSH Keys
```

### "Branch 'main' doesn't track 'origin/main'"
```bash
git branch --set-upstream-to=origin/main main
```

### Custom Domain Setup (if hosting elsewhere)
1. Update DNS records to point to your hosting provider
2. Configure SSL certificate (usually auto with modern hosts)
3. Set GitHub Pages to use custom domain in repository settings

## Monitoring & Maintenance

### GitHub Actions (CI/CD)
You can add automated testing/deployment:
```bash
# Create .github/workflows/test.yml for automated checks
```

### Regular Updates
- Keep PHP version current
- Update database backups regularly
- Monitor for security vulnerabilities
- Review logs for errors/attacks

## Questions?

Refer to the main README.md for development setup and features documentation.

---

**Last Updated**: April 2026
