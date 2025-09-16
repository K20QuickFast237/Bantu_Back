# 🚀 Matching Service Backend

**Service de matching intelligent** entre candidats et offres d'emploi utilisant l'IA sémantique.

## 📋 Prérequis

- **PHP** 8.1+ avec extensions : `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- **Composer** 2.0+
- **MySQL** 8.0+ ou **MariaDB** 10.3+
- **Python** 3.10+ (pour le microservice IA)


## 🛠️ Installation (pour l'équipe)

### Étape 1 : Récupération du code
```bash
# Récupérer les dernières modifications
git pull origin main
```

### Étape 2 : Configuration de l'environnement
```bash
# Copier le fichier d'environnement (si pas déjà fait)
cp .env.example .env

# Éditer .env avec vos paramètres de base de données
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bantu_back
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### Étape 3 : Installation des dépendances Laravel
```bash
# Installer les dépendances PHP
composer install

# Générer la clé d'application
php artisan key:generate

# Configurer Passport
php artisan passport:install
```

### Étape 4 : Configuration de la base de données
```bash
# Exécuter les nouvelles migrations
php artisan migrate

# Peupler la base avec des données de test (si nécessaire)
php artisan db:seed
```

### Étape 5 : Installation du microservice IA (Python)
```bash
# Aller dans le dossier du microservice
cd ml_service

# Installer les dépendances Python
pip install fastapi uvicorn sentence-transformers scikit-learn

# Lancer le microservice IA
python app.py
```

### Étape 6 : Lancement de l'application
```bash
# Retourner à la racine
cd ..

# Lancer Laravel
php artisan serve
```

## 🔄 Mise à jour quotidienne (pour l'équipe)
```bash
# 1. Récupérer les dernières modifications
git pull origin main

# 2. Installer les nouvelles dépendances (si nécessaire)
composer install

# 3. Exécuter les nouvelles migrations (si nécessaire)
php artisan migrate

# 4. Redémarrer les services
php artisan serve
```

## ⚠️ Notes importantes
- **Première installation** : Suivez toutes les étapes 1-6
- **Mises à jour quotidiennes** : Utilisez la section "Mise à jour quotidienne"
- **Problèmes de migration** : Contactez l'équipe avant de faire `migrate:fresh`

## 🎯 API de Matching

### Routes disponibles
- `GET /api/matching/candidate/{candidateId}` - Offres recommandées pour un candidat
- `GET /api/matching/job/{offreId}` - Candidats recommandés pour une offre

### Test avec Postman
1. **Inscription** : `POST /api/register`
2. **Connexion** : `POST /api/login` 
3. **Créer profil** : `POST /api/profile/particulier` ou `POST /api/profile/professionnel`
4. **Tester matching** : `GET /api/matching/candidate/{id}` et `GET /api/matching/job/{id}` ( vous pouvez tester sans vous connecter)

## 🔧 Technologies utilisées


- **IA** : Python FastAPI + Sentence-Transformers
- **Base de données** : MySQL/MariaDB
- **Matching** : Embeddings sémantiques + Similarité cosinus

## 📊 Fonctionnalités

✅ **Matching intelligent** - IA sémantique sans dictionnaires  
✅ **Multi-secteurs** - Fonctionne pour tous les métiers  
✅ **Tolérance aux fautes** - "js" ≈ "javascript"  
✅ **API REST** - Prêt pour mobile/web  

