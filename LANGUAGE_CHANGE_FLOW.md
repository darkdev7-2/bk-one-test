# 🔄 Flow Technique Détaillé : Changement de Langue

Ce document décrit **EXACTEMENT** ce qui se passe techniquement, ligne par ligne, lorsqu'un utilisateur change la langue sur le site.

---

## 📋 Vue d'Ensemble du Flow

```
┌─────────────────────────────────────────────────────────────┐
│  1. Utilisateur clique sur le dropdown de langue           │
│  2. Sélectionne une nouvelle langue (ex: Français)         │
│  3. JavaScript déclenche la navigation                     │
│  4. Requête HTTP vers /language-update                     │
│  5. Controller met à jour la session                       │
│  6. Redirection vers la page précédente                    │
│  7. Middleware Localization charge la langue               │
│  8. Laravel charge les fichiers de traduction              │
│  9. Les vues utilisent __() pour afficher les textes       │
│  10. Page complètement traduite affichée à l'utilisateur   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎬 Étape par Étape - Scénario Complet

### Contexte Initial

**Utilisateur** : Marie visite le site
**Langue actuelle** : English (par défaut)
**Page** : Dashboard utilisateur (`/user/dashboard`)
**Action** : Change la langue vers Français

---

## 📍 ÉTAPE 1 : Affichage du Dropdown

### Fichier : `resources/views/frontend/default/include/__user_header.blade.php`

**Ligne 41-51** :

```blade
@if(setting('language_switcher'))
    <div class="language-switcher">
        <select class="langu-swit small" name="language" id=""
                onchange="window.location.href=this.options[this.selectedIndex].value;">
            @foreach(\App\Models\Language::where('status',true)->get() as $lang)
                <option
                    value="{{ route('language-update',['name'=> $lang->locale]) }}"
                    @selected( app()->getLocale() == $lang->locale )>
                    {{$lang->name}}
                </option>
            @endforeach
        </select>
    </div>
@endif
```

### Ce qui se passe :

1. **Vérification du setting** :
   ```php
   setting('language_switcher') // Vérifie si activé dans la BDD
   ```

2. **Requête à la BDD** :
   ```sql
   SELECT * FROM languages WHERE status = 1
   ```
   **Résultat** :
   ```
   id | name      | locale | status | is_default
   1  | English   | en     | 1      | 1
   2  | Français  | fr     | 1      | 0
   3  | Español   | es     | 1      | 0
   ```

3. **Génération du HTML** :
   ```html
   <select name="language" onchange="window.location.href=this.options[this.selectedIndex].value;">
       <option value="/language-update?name=en" selected>English</option>
       <option value="/language-update?name=fr">Français</option>
       <option value="/language-update?name=es">Español</option>
   </select>
   ```

4. **Affichage navigateur** :
   ```
   ┌──────────────┐
   │ English   ▼  │
   └──────────────┘
   ```

---

## 📍 ÉTAPE 2 : Utilisateur Change la Langue

### Action Utilisateur

Marie clique sur le dropdown et sélectionne **Français**

### JavaScript Déclenché

```javascript
onchange="window.location.href=this.options[this.selectedIndex].value;"
```

**Traduction** : Quand la sélection change, redirige vers l'URL de l'option sélectionnée

**Valeur de l'option** : `/language-update?name=fr`

### Navigation Déclenchée

```
Navigateur : GET http://exemple.com/language-update?name=fr
```

---

## 📍 ÉTAPE 3 : Requête HTTP Reçue

### Serveur Web (Nginx/Apache)

```
Requête reçue : GET /language-update?name=fr
Headers :
  - Cookie: laravel_session=eyJpdiI6Ijdk...
  - Referer: /user/dashboard
  - User-Agent: Mozilla/5.0...
```

### Laravel Router

**Fichier** : `routes/web.php` (ligne 221)

```php
Route::get('language-update', [HomeController::class, 'languageUpdate'])
    ->name('language-update');
```

**Match trouvé** ✅
**Controller** : `App\Http\Controllers\Frontend\HomeController`
**Méthode** : `languageUpdate`

---

## 📍 ÉTAPE 4 : Controller Traite la Requête

### Fichier : `app/Http/Controllers/Frontend/HomeController.php`

**Ligne 66-71** :

```php
public function languageUpdate(Request $request)
{
    session()->put('locale', $request->name);

    return redirect()->back();
}
```

### Exécution Détaillée

**1. Récupération du paramètre** :
```php
$request->name  // Valeur : "fr"
```

**2. Mise à jour de la session** :
```php
session()->put('locale', 'fr');
```

**Ce qui se passe en interne** :
```php
// Laravel écrit dans la session
$_SESSION['locale'] = 'fr';

// Session sauvegardée dans :
// - Fichier : storage/framework/sessions/abc123...
// - Ou Redis/Database selon config
```

**Contenu de la session après** :
```php
[
    '_token' => 'xyz789...',
    'locale' => 'fr',  // ← NOUVEAU !
    'url' => [
        'intended' => '/user/dashboard'
    ],
    'login_web_xxx' => 42,  // ID utilisateur
    // ... autres données
]
```

**3. Redirection vers la page précédente** :
```php
return redirect()->back();
```

**Laravel détecte le Referer** :
```
HTTP/1.1 302 Found
Location: /user/dashboard
Set-Cookie: laravel_session=eyJpdiI6Ijdk...; path=/; httponly
```

---

## 📍 ÉTAPE 5 : Nouvelle Requête (Redirection)

### Navigateur Suit la Redirection

```
Navigateur : GET http://exemple.com/user/dashboard
Headers :
  - Cookie: laravel_session=eyJpdiI6Ijdk... (session mise à jour)
```

### Laravel Traite la Nouvelle Requête

**1. Session démarrée** :
```php
// Middleware : StartSession
$_SESSION = unserialize(file_get_contents('storage/framework/sessions/abc123...'));
// Contient maintenant : 'locale' => 'fr'
```

**2. Middleware Localization** (si appliqué) :

**Fichier** : `app/Http/Middleware/Localization.php`

```php
public function handle(Request $request, Closure $next)
{
    if (session()->has('locale')) {
        App::setLocale(session()->get('locale'));
    }

    return $next($request);
}
```

**Exécution** :
```php
session()->has('locale')  // true
session()->get('locale')  // "fr"

App::setLocale('fr');  // ← LANGUE CHANGÉE GLOBALEMENT !
```

**3. Configuration Laravel mise à jour** :
```php
// config('app.locale') reste "en" (défaut)
// Mais app()->getLocale() retourne maintenant "fr"
```

---

## 📍 ÉTAPE 6 : Chargement des Fichiers de Traduction

### Laravel Translation System

**Fichiers chargés automatiquement** :

```
resources/lang/fr/
├── validation.php    ← Messages de validation
├── auth.php          ← Messages d'authentification
├── passwords.php     ← Messages de mot de passe
└── pagination.php    ← Textes de pagination

resources/lang/app/
└── fr.json           ← Traductions app (notre nouveau système)
```

### Chargement en Mémoire

**1. Fichiers PHP** :
```php
// Laravel charge : resources/lang/fr/validation.php
return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    // ... 200+ lignes
];
```

**2. Fichier JSON** :
```php
// Laravel charge : resources/lang/app/fr.json
{
    "welcome": {
        "title": "Bienvenue à",
        "appName": "Digi Bank",
        "tagline": "La banque rendue simple, sécurisée et fluide."
    },
    "signIn": {
        "welcomeText": "Bon retour !",
        "emailLabel": "Email ou nom d'utilisateur",
        "passwordLabel": "Mot de passe"
    }
    // ... 500+ lignes
}
```

**3. Mise en cache** :
```php
// Laravel met en cache en mémoire pour la requête
$translationsCache = [
    'validation.required' => 'Le champ :attribute est obligatoire.',
    'welcome.title' => 'Bienvenue à',
    'signIn.welcomeText' => 'Bon retour !',
    // ...
];
```

---

## 📍 ÉTAPE 7 : Rendu des Vues avec Traductions

### Controller Dashboard

**Fichier** : `app/Http/Controllers/Frontend/DashboardController.php`

```php
public function dashboard()
{
    // ... logique métier ...

    return view('frontend::dashboard.index', compact('data'));
}
```

### Vue Blade

**Fichier** : `resources/views/frontend/default/dashboard/index.blade.php`

**Avant traduction (code Blade)** :
```blade
<h1>{{ __('Dashboard') }}</h1>
<p>{{ __('Welcome back!') }}</p>
<button>{{ __('Deposit') }}</button>
<button>{{ __('Withdraw') }}</button>
```

### Fonction `__()` en Action

**Pour chaque appel** :

```php
__('Dashboard')
  ↓
1. Laravel détecte la locale actuelle : app()->getLocale() → "fr"

2. Cherche dans les fichiers de traduction :
   - resources/lang/fr/messages.php  (si existe)
   - resources/lang/app/fr.json

3. Trouve la clé "Dashboard" dans fr.json :
   "Dashboard": "Tableau de bord"

4. Retourne : "Tableau de bord"
```

**Exemple complet** :

```php
// Code Blade
{{ __('Welcome back!') }}

// Étapes internes
__('Welcome back!')
  → App::getLocale() = "fr"
  → Cherche dans fr.json
  → Trouve : "Welcome back!": "Bon retour !"
  → Retourne : "Bon retour !"

// HTML généré
Bon retour !
```

### HTML Final Généré

**Avant (English)** :
```html
<h1>Dashboard</h1>
<p>Welcome back!</p>
<button>Deposit</button>
<button>Withdraw</button>
```

**Après (Français)** :
```html
<h1>Tableau de bord</h1>
<p>Bon retour !</p>
<button>Dépôt</button>
<button>Retrait</button>
```

---

## 📍 ÉTAPE 8 : Réponse Envoyée au Navigateur

### HTTP Response

```http
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
Set-Cookie: laravel_session=eyJpdiI6Ijdk...; path=/; httponly

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Tableau de bord - Digi Bank</title>
</head>
<body>
    <h1>Tableau de bord</h1>
    <p>Bon retour !</p>
    ...
</body>
</html>
```

### Navigateur Affiche

```
┌────────────────────────────────────┐
│  🏦 Digi Bank                      │
├────────────────────────────────────┤
│  [Français ▼] 🔔 👤               │
├────────────────────────────────────┤
│                                    │
│  Tableau de bord                   │
│  ─────────────                     │
│                                    │
│  Bon retour !                      │
│                                    │
│  [Dépôt]  [Retrait]  [Transfert]  │
│                                    │
└────────────────────────────────────┘
```

---

## 🔄 Persistance de la Langue

### Sessions Suivantes

**1. Utilisateur navigue vers une autre page** :
```
GET /user/transactions
  → Session contient toujours : 'locale' => 'fr'
  → Middleware Localization applique : App::setLocale('fr')
  → Page en français ✅
```

**2. Utilisateur se déconnecte et revient demain** :
```
GET /
  → Session existe toujours (cookie laravel_session)
  → Session contient : 'locale' => 'fr'
  → Page d'accueil en français ✅
```

**3. Utilisateur efface ses cookies** :
```
GET /
  → Nouvelle session créée
  → Pas de 'locale' dans la session
  → Langue par défaut utilisée (is_default = 1)
  → Page en anglais
```

---

## 🎯 Impact de la Traduction Automatique

### AVANT la Traduction Automatique

**Fichier** : `resources/lang/app/fr.json` (après création manuelle de la langue)

```json
{
    "Dashboard": "Dashboard",  ← EN ANGLAIS !
    "Welcome back!": "Welcome back!",  ← EN ANGLAIS !
    "Deposit": "Deposit",  ← EN ANGLAIS !
    "Withdraw": "Withdraw"  ← EN ANGLAIS !
}
```

**Résultat pour l'utilisateur** :
```
Tableau de bord → "Dashboard" (anglais) ❌
Bon retour ! → "Welcome back!" (anglais) ❌
```

### APRÈS la Traduction Automatique

**Commande exécutée** :
```bash
php artisan translate:language fr
# Ou via Admin : Auto Translate > Translate
```

**Fichier** : `resources/lang/app/fr.json` (après traduction auto)

```json
{
    "Dashboard": "Tableau de bord",  ← TRADUIT ! ✅
    "Welcome back!": "Bon retour !",  ← TRADUIT ! ✅
    "Deposit": "Dépôt",  ← TRADUIT ! ✅
    "Withdraw": "Retrait"  ← TRADUIT ! ✅
}
```

**Résultat pour l'utilisateur** :
```
Tableau de bord → "Tableau de bord" (français) ✅
Bon retour ! → "Bon retour !" (français) ✅
```

### Ce qui se passe lors de la traduction auto

```
1. Admin lance : translate:language fr

2. AutoTranslationService :
   ├─ Lit : resources/lang/app/en.json
   ├─ Extrait toutes les valeurs : ["Dashboard", "Welcome back!", ...]
   ├─ Envoie à Google Translate API : en → fr
   ├─ Reçoit : ["Tableau de bord", "Bon retour !", ...]
   ├─ Préserve les placeholders : :attribute, {variable}
   └─ Écrit : resources/lang/app/fr.json

3. Prochaine requête utilisateur :
   └─ Laravel charge fr.json (mis à jour)
   └─ Traductions françaises affichées ✅
```

---

## 🔍 Cas Spéciaux

### Cas 1 : Traduction avec Variables

**Blade** :
```blade
{{ __('Welcome back, :name!', ['name' => $user->name]) }}
```

**Fichier JSON** :
```json
{
    "Welcome back, :name!": "Bon retour, :name !"
}
```

**Rendu** :
```
Utilisateur : Marie
Résultat : "Bon retour, Marie !"
```

**Comment la traduction auto préserve `:name`** :
```
1. AutoTranslationService détecte : ":name" (pattern :[\w]+)
2. Remplace temporairement : "Welcome back, ___PLACEHOLDER_0___!"
3. Envoie à Google : "Welcome back, ___PLACEHOLDER_0___!"
4. Reçoit : "Bon retour, ___PLACEHOLDER_0___ !"
5. Restaure : "Bon retour, :name !"
```

### Cas 2 : Validation Laravel

**Blade** :
```blade
@error('email')
    <span>{{ $message }}</span>
@enderror
```

**Validation échoue** :
```php
$validator = Validator::make($request->all(), [
    'email' => 'required|email'
]);
```

**Laravel cherche dans** : `resources/lang/fr/validation.php`

```php
return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'attributes' => [
        'email' => 'adresse e-mail'
    ]
];
```

**Message affiché** :
```
"L'adresse e-mail est obligatoire."
"L'adresse e-mail doit être une adresse e-mail valide."
```

### Cas 3 : Textes Hardcodés (Non traduits)

**Mauvais code** :
```blade
<h1>Dashboard</h1>  ← Hardcodé, ne sera JAMAIS traduit
```

**Bon code** :
```blade
<h1>{{ __('Dashboard') }}</h1>  ← Traduit automatiquement
```

---

## 📊 Schéma Récapitulatif Complet

```
┌─────────────────────────────────────────────────────────────┐
│  UTILISATEUR                                                │
│  Clique sur dropdown → Sélectionne "Français"              │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│  NAVIGATEUR                                                 │
│  JavaScript : window.location.href = "/language-update?name=fr" │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼ GET /language-update?name=fr
┌─────────────────────────────────────────────────────────────┐
│  LARAVEL ROUTER                                             │
│  Route trouvée : HomeController::languageUpdate             │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│  CONTROLLER                                                 │
│  session()->put('locale', 'fr')                             │
│  return redirect()->back()                                  │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼ 302 Redirect → /user/dashboard
┌─────────────────────────────────────────────────────────────┐
│  NAVIGATEUR                                                 │
│  Suit la redirection                                        │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼ GET /user/dashboard (avec session mise à jour)
┌─────────────────────────────────────────────────────────────┐
│  MIDDLEWARE LOCALIZATION                                    │
│  Lit : session('locale') → "fr"                             │
│  Applique : App::setLocale('fr')                            │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│  LARAVEL TRANSLATION LOADER                                 │
│  Charge : resources/lang/fr/*.php                           │
│  Charge : resources/lang/app/fr.json                        │
│  Met en cache en mémoire                                    │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│  CONTROLLER DASHBOARD                                       │
│  Logique métier                                             │
│  return view('dashboard.index')                             │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────────────────────────┐
│  BLADE ENGINE                                               │
│  Pour chaque __('text') :                                   │
│    1. Récupère locale actuelle (fr)                         │
│    2. Cherche dans fichiers de traduction                   │
│    3. Remplace par traduction française                     │
│  Génère HTML final en français                              │
└────────────┬────────────────────────────────────────────────┘
             │
             ▼ HTTP Response (HTML en français)
┌─────────────────────────────────────────────────────────────┐
│  NAVIGATEUR                                                 │
│  Affiche la page complètement en français                   │
└─────────────────────────────────────────────────────────────┘
```

---

## ⏱️ Performance et Timing

### Temps d'Exécution

**1. Changement de langue** : ~50-100ms
```
- Requête HTTP : 10-20ms
- Controller : 5ms
- Session write : 10-20ms
- Redirection : 5ms
- Requête suivante : 10-20ms
```

**2. Chargement des traductions** : ~20-50ms (première fois)
```
- Lecture fichiers : 10-20ms
- Parsing JSON : 5-10ms
- Mise en cache : 5-10ms
- Requêtes suivantes : < 1ms (cache mémoire)
```

**3. Rendu des vues** : ~50-200ms
```
- Compilation Blade : 20-50ms
- Traductions (si cache) : < 1ms
- Génération HTML : 30-150ms
```

**Total** : ~120-350ms (perçu comme instantané)

---

## 🎁 Bonus : Application Mobile (API)

### Flow API

```
1. Client mobile : POST /api/change-language/fr

2. Controller API :
   session()->put('locale', 'fr')
   return response()->json([
       'status' => true,
       'locale' => 'fr',
       'translations_keys' => file_get_contents('resources/lang/app/fr.json')
   ])

3. App mobile reçoit TOUT le JSON

4. App stocke localement (SQLite, AsyncStorage, etc.)

5. App applique les traductions immédiatement

6. Requêtes API suivantes :
   Header : Accept-Language: fr
   OU : Cookie avec session
```

---

## ✅ Checklist de Vérification

Pour qu'une traduction fonctionne, il faut :

- ✅ Langue créée dans `languages` table
- ✅ `status = 1` (active)
- ✅ Fichier `resources/lang/app/{locale}.json` existe
- ✅ Fichiers `resources/lang/{locale}/*.php` existent
- ✅ Textes dans les vues utilisent `__()` ou `@lang()`
- ✅ Middleware Localization appliqué (ou App::setLocale appelé)
- ✅ Session disponible (cookies activés)
- ✅ Language switcher enabled : `setting('language_switcher') = true`

---

**Document créé pour** : BK-ONE-TEST Banking Platform
**Objectif** : Comprendre le flow technique exact du changement de langue
**Date** : Novembre 2025
