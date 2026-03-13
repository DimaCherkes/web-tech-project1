# Technická správa - Olympijské hry APP

## 0. GitHub: 
all commit history is available here: 
```url
github.com/DimaCherkes/web-tech-project1
```

Táto správa popisuje technické riešenie, konfiguráciu a postup nasadenia aplikácie pre správu údajov o olympijských športovcoch.

## 1. Konfigurácia servera (VPS) a Nginx

Pre správne fungovanie aplikácie v podadresári `/project1/` a podporu „pekných“ URL (routing) boli v konfigurácii Nginx vykonané nasledujúce zmeny:

*   **Smerovanie na router**: Pridaná direktíva `try_files` pre presmerovanie všetkých požiadaviek, ktoré nie sú fyzickými súbormi, na `index.php`.
*   **Podpora podadresára**: Konfigurácia `location /project1` umožňuje aplikácii bežať v špecifickom priečinku pri zachovaní funkčnosti smerovača.

### Príklad relevantnej časti Nginx konfigurácie:
```nginx
location /project1 {
    try_files $uri $uri/ /project1/index.php?$query_string;
}
```

## 2. Inštalované systémové balíky

Aplikácia vyžaduje PHP 8.3+ s nasledujúcimi rozšíreniami:
*   `pdo_mysql`: Pre komunikáciu s MariaDB/MySQL databázou.
*   `gd`: Pre generovanie/spracovanie obrázkov (vyžadované knižnicou pre QR kódy).
*   `curl`: Pre komunikáciu s Google API.
*   `unzip`, `git`: Pre inštaláciu závislostí cez Composer.
*   **Composer**: Nástroj na správu PHP knižníc.

## 3. Použité knižnice a frameworky

Projekt nepoužíva žiadny veľký framework (ako Laravel), ale stavia na vlastnej architektúre **Controller-Service-Repository**. Na špecifické funkcie boli použité tieto knižnice:

*   **`robthree/twofactorauth`**: Implementácia dvojfaktorovej autentifikácie (2FA).
*   **`bacon/bacon-qr-code`**: Generovanie QR kódov pre 2FA.
*   **`google/apiclient`**: Integrácia prihlásenia cez Google (OAuth2).
*   **Vanilla JS & CSS**: Frontend je postavený na čistom JavaScripte a CSS bez externých frameworkov pre zachovanie rýchlosti a minimalistického dizajnu.

## 4. Postup nasadenia

### Krok 1: Príprava súborov
1. Všetko je v GitHube, môžete si stiahnuť súbory priamo z gitu do priečinka `/var/www/node47.webte.fei.stuba.sk/project1`.
   - ```shell
     git clone https://github.com/DimaCherkes/web-tech-project1.git 
2. **DÔLEŽITÉ**: Súbor `client_secret_webte.json` (stiahnutý z Google Cloud Console) musí byť umiestnený v priečinku `/var/www/` (o úroveň vyššie nad priečinkom domény), na rovnakom mieste ako súbor `config.php`. Bez tohto súboru nebude fungovať prihlásenie cez Google.

### Krok 2: Inštalácia závislostí
V priečinku `project1/` spustite príkaz:
```bash
composer update
```
Tento príkaz vytvorí priečinok `vendor/` a nainštaluje všetky potrebné knižnice podľa `composer.lock`.

### Krok 3: Nastavenie databázy
1. Vytvorte databázu (napr. `ogames_app`).
2. Importujte SQL schémy v tomto poradí:
    *   Základná schéma (tabuľky pre športovcov, krajiny, disciplíny).
    *   Schéma pre používateľov (`user_accounts`).
    *   Schéma pre históriu prihlásení (`login_history`).
3. V súbore `config.php` (o 2 úrovňi vyššie nad `project1/`) nastavte prístupové údaje k databáze.

### Krok 4: Práva k súborom
Nastavte vlastníctvo súborov pre webový server:
```bash
sudo chown -R www-data:www-data /var/www/node47.webte.fei.stuba.sk/project1
sudo chmod -R 755 /var/www/node47.webte.fei.stuba.sk/project1
```

### Krok 5: Konfigurácia Google OAuth
V Google Cloud Console nastavte **Authorized redirect URIs** na:
`https://node47.webte.fei.stuba.sk/project1/oauth2callback.php`

## 5. Architektúra aplikácie

Aplikácia je logicky rozdelená:
*   **`index.php`**: Centrálny router, spracováva požiadavky a normalizuje cesty.
*   **`controller/`**: Riadenie toku aplikácie, overovanie typu požiadavky (GET/POST).
*   **`service/`**: Biznis logika (validácia registrácie, overovanie 2FA, komunikácia s Google API).
*   **`repository/`**: SQL dopyty a priama práca s databázou.
*   **`view/`**: HTML šablóny naformátované pomocou partials (spoločný header).
*   **`dto/`**: Objekty pre prenos údajov medzi vrstvami.
