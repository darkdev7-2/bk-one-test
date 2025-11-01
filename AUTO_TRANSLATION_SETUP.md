# Guide de Configuration - Traduction Automatique

Ce guide vous explique comment configurer et utiliser la fonctionnalité de traduction automatique avec Google Translate API.

## 📋 Prérequis

- Compte Google Cloud Platform
- Carte de crédit (pour activer l'API, mais un niveau gratuit existe)
- Accès aux fichiers de configuration du projet

## 🚀 Installation

### Étape 1 : Obtenir une clé API Google Translate

1. **Accédez à Google Cloud Console**
   - Allez sur https://console.cloud.google.com/

2. **Créez ou sélectionnez un projet**
   - Cliquez sur le sélecteur de projet en haut
   - Créez un nouveau projet ou sélectionnez-en un existant

3. **Activez l'API Cloud Translation**
   - Dans le menu de navigation, allez à "APIs & Services" > "Library"
   - Recherchez "Cloud Translation API"
   - Cliquez sur "Enable"

4. **Créez une clé API**
   - Allez à "APIs & Services" > "Credentials"
   - Cliquez sur "Create Credentials" > "API Key"
   - Copiez la clé générée
   - (Recommandé) Restreignez la clé à l'API Cloud Translation uniquement

### Étape 2 : Configurer l'application

1. **Ajoutez la clé API au fichier .env**

```bash
# Ouvrez votre fichier .env et ajoutez :
GOOGLE_TRANSLATE_API_KEY=votre_clé_api_ici
AUTO_TRANSLATION_DRIVER=google
AUTO_TRANSLATION_SOURCE=en
AUTO_TRANSLATION_LOG=true
```

2. **Effacez le cache de configuration**

```bash
php artisan config:clear
php artisan cache:clear
```

## 📖 Utilisation

### Via l'Interface Admin

1. **Accédez à la page de gestion des langues**
   - Connectez-vous à l'admin
   - Allez dans Settings > Language Settings
   - Cliquez sur le bouton "Auto Translate"

2. **Traduire une langue spécifique**
   - Dans la liste des langues, cliquez sur "Translate" pour traduire uniquement les éléments manquants
   - Ou cliquez sur "Force Retranslate" pour retraduire tout (⚠️ écrase les traductions existantes)

3. **Traduire toutes les langues**
   - Cliquez sur "Translate All Languages" pour traduire toutes les langues actives
   - Utilisez "Force Retranslate All" avec précaution

### Via la Ligne de Commande

```bash
# Traduire une langue spécifique (ex: français)
php artisan translate:language fr

# Force la retraduction (écrase les traductions existantes)
php artisan translate:language fr --force

# Traduire toutes les langues actives
php artisan translate:language en --all

# Force la retraduction de toutes les langues
php artisan translate:language en --all --force
```

## ⚙️ Configuration Avancée

Le fichier de configuration se trouve à `config/auto-translation.php`.

### Options disponibles

```php
// Driver de traduction
'driver' => 'google',  // Actuellement seul Google est supporté

// Langue source (généralement anglais)
'source_locale' => 'en',

// Taille des lots pour l'API (max 128 pour Google)
'batch_size' => 100,

// Délai entre les requêtes (en millisecondes)
'rate_limit_delay' => 100,

// Configuration de retry
'retry' => [
    'attempts' => 3,
    'delay' => 1000, // en millisecondes
],

// Activer les logs
'log_enabled' => true,
```

### Patterns préservés

Les patterns suivants ne sont **pas traduits** et sont préservés :

- `:attribute` - Placeholders Laravel
- `{variable}` - Variables entre accolades
- `[app_name]` - Variables entre crochets
- `@mentions` - Mentions
- `#hashtags` - Hashtags

## 💰 Coûts

### Tarification Google Translate API

- **Gratuit** : Premiers 500,000 caractères par mois
- **Payant** : ~$20 par million de caractères après le seuil gratuit

### Estimation des coûts

Pour une application moyenne :
- ~1000 clés de traduction
- ~50 caractères par clé en moyenne
- 5 langues

**Coût estimé** : ~$0.01 - $0.05 par langue (très abordable)

## 🔍 Que sera traduit ?

La fonctionnalité traduit automatiquement :

1. **Fichiers Laravel** (`resources/lang/{locale}/*.php`)
   - Fichiers de validation
   - Messages système
   - Autres fichiers PHP de langue

2. **Fichiers App JSON** (`resources/lang/app/{locale}.json`)
   - Traductions spécifiques à l'application mobile/frontend

3. **Contenu préservé**
   - Les placeholders Laravel (`:attribute`, etc.)
   - Les variables (`{name}`, `[app]`, etc.)
   - Les balises HTML (si présentes)

## 🛠️ Dépannage

### Erreur : "API key not configured"

- Vérifiez que `GOOGLE_TRANSLATE_API_KEY` est bien défini dans `.env`
- Exécutez `php artisan config:clear`

### Erreur : "API key not valid"

- Vérifiez que la clé API est correcte
- Assurez-vous que l'API Cloud Translation est activée dans Google Cloud Console
- Vérifiez que la clé n'a pas de restrictions qui empêchent son utilisation

### Erreur : "Quota exceeded"

- Vous avez dépassé le quota gratuit
- Activez la facturation dans Google Cloud Console
- Ou attendez le mois prochain pour le quota gratuit

### Traductions de mauvaise qualité

- Google Translate n'est pas parfait
- Révisez et corrigez manuellement les traductions importantes
- Utilisez le mode "Translate" (non-force) pour préserver vos corrections

### Les placeholders sont traduits

- Vérifiez la configuration dans `config/auto-translation.php`
- Les patterns de préservation peuvent être personnalisés

## 📝 Bonnes Pratiques

1. **Testez d'abord sur une langue**
   - Avant de traduire toutes les langues, testez sur une seule

2. **Utilisez le mode normal** (non-force)
   - Cela préserve vos traductions manuelles existantes

3. **Révisez les traductions**
   - La traduction automatique est un point de départ
   - Révisez les textes importants manuellement

4. **Sauvegardez avant force retranslate**
   - Le mode force écrase tout
   - Faites une sauvegarde de votre base de données

5. **Surveillez les coûts**
   - Consultez régulièrement votre console Google Cloud
   - Configurez des alertes de budget

## 🔄 Workflow Recommandé

1. **Ajoutez une nouvelle langue** dans l'admin
2. **Utilisez "Translate"** pour la traduction initiale
3. **Révisez manuellement** les textes clés (page d'accueil, messages importants)
4. **Testez** l'application dans cette langue
5. **Corrigez** les erreurs via l'interface de gestion des mots-clés
6. **Répétez** pour les autres langues

## 📚 Ressources

- [Documentation Google Cloud Translation](https://cloud.google.com/translate/docs)
- [Tarification Google Translate](https://cloud.google.com/translate/pricing)
- [Support des langues](https://cloud.google.com/translate/docs/languages)

## 🆘 Support

Pour toute question ou problème :
1. Vérifiez ce guide
2. Consultez les logs : `storage/logs/laravel.log`
3. Contactez l'équipe de développement

---

**Dernière mise à jour** : Novembre 2025
**Version** : 1.0
