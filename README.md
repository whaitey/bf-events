# BF Events - WordPress Plugin

Ez a WordPress plugin egy teljes körű eseménykezelő rendszert biztosít, amely lehetővé teszi az események, előadók és naptár kezelését.

## Funkciók

### 🎯 Esemény kezelés
- **Esemény létrehozás**: Részletes esemény információk kezelése
- **Előadó kezelés**: Előadók és moderátorok hozzáadása
- **Színpad kategóriák**: Események kategorizálása színpadok szerint
- **Időpont kezelés**: Kezdési és befejezési időpontok

### 🎨 Naptár és megjelenítés
- **Naptár nézet**: Események naptár formátumban
- **Lista nézet**: Események listázása szűrőkkel
- **Reszponzív design**: Mobilbarát felület
- **AJAX szűrés**: Dinamikus szűrés oldal újratöltés nélkül

### 📅 Naptár integráció
- **Add to Calendar**: Események hozzáadása különböző naptárakhoz
- **iCal export**: Események exportálása iCal formátumban
- **Google Calendar**: Közvetlen hozzáadás Google Calendar-hoz

### 🎤 Előadó oldalak
- **Előadó profilok**: Részletes előadó információk
- **Kapcsolódó események**: Előadó eseményeinek listázása
- **Kontakt információk**: Közösségi média linkek

## Telepítés

### 1. Fájlok feltöltése
1. Töltsd fel a plugin fájljait a WordPress `wp-content/plugins/bf-events/` mappába
2. Vagy csomagold be a fájlokat ZIP formátumban és telepítsd a WordPress admin panelen keresztül

### 2. Plugin aktiválása
1. Menj a WordPress admin panelbe
2. Navigálj a **Beépülő modulok** > **Telepített beépülő modulok** menüpontra
3. Keresd meg a "BF Events" beépülő modult
4. Kattints az **Aktiválás** gombra

### 3. Carbon Fields telepítése
A plugin Carbon Fields-t használ a mezők kezeléséhez. Telepítsd a Carbon Fields plugint is.

## Használat

### 1. Esemény létrehozása
1. Menj az **Események** > **Új esemény** menüpontra
2. Töltsd ki az esemény adatait:
   - **Cím**: Esemény neve
   - **Leírás**: Részletes leírás
   - **Időpont**: Kezdési és befejezési idő
   - **Színpad**: Válaszd ki a színpadot
   - **Előadók**: Add hozzá az előadókat
   - **Moderátorok**: Add hozzá a moderátorokat

### 2. Előadó létrehozása
1. Menj az **Előadók** > **Új előadó** menüpontra
2. Töltsd ki az előadó adatait:
   - **Név**: Vezetéknév és keresztnév
   - **Pozíció**: Munkahelyi pozíció
   - **Cég**: Munkahely
   - **Avatar**: Profilkép
   - **Leírás**: Részletes bemutatkozás
   - **Kontakt információk**: Közösségi média linkek

### 3. Shortcode használata
A plugin több shortcode-ot biztosít:

```
[bf_events_calendar] - Naptár nézet
[bf_events_list] - Lista nézet
[bf_speakers_list] - Előadók listája
```

## Custom Post Types

A plugin a következő custom post type-okat hozza létre:
- **bsf_event**: Események
- **bsf_speaker**: Előadók

## Taxonomiák

- **bsf_stage**: Színpad kategóriák

## Custom Fields

A plugin Carbon Fields segítségével kezeli a következő mezőket:

### Események
- `bsf_start_time`: Kezdési időpont
- `bsf_end_time`: Befejezési időpont
- `bsf_speakers`: Előadók
- `bsf_moderators`: Moderátorok
- `bsf_featured`: Kiemelt esemény

### Előadók
- `bsf_first_name`: Keresztnév
- `bsf_last_name`: Vezetéknév
- `bsf_title`: Pozíció
- `bsf_company`: Cég
- `bsf_avatar`: Profilkép
- `bsf_speaker_description`: Leírás
- `bsf_contact_info`: Kontakt információk

## JavaScript API

```javascript
// Események szűrése
bfFilterEvents(filters);

// Naptár navigáció
bfCalendarNavigation(direction);

// Add to Calendar
bfAddToCalendar(eventId, calendarType);
```

## CSS osztályok

A plugin a következő CSS osztályokat használja:
- `.bsf-container`: Fő konténer
- `.bsf-event-card`: Esemény kártya
- `.bsf-speaker-card`: Előadó kártya
- `.bsf-calendar`: Naptár konténer
- `.bsf-filters`: Szűrők konténer

## AJAX funkciók

- **Esemény szűrés**: Dinamikus szűrés
- **Naptár navigáció**: Hónap váltás
- **Add to Calendar**: Naptár hozzáadás
- **Előadó keresés**: Előadók keresése

## Hibaelhárítás

### Események nem jelennek meg
1. Ellenőrizd, hogy az események publikálva vannak-e
2. Nézd meg a shortcode paramétereket
3. Ellenőrizd a szűrő beállításokat

### Naptár nem működik
1. Ellenőrizd a JavaScript fájlok betöltődését
2. Nézd meg a böngésző konzolját
3. Ellenőrizd az AJAX URL beállításokat

### Carbon Fields hibák
1. Ellenőrizd, hogy a Carbon Fields telepítve van-e
2. Frissítsd a Carbon Fields-t
3. Ellenőrizd a mező konfigurációkat

## Verzió információk

- **Verzió**: 1.0.0
- **PHP verzió**: 7.0+
- **WordPress verzió**: 5.0+
- **Carbon Fields**: 3.0+

## Licenc

Ez a plugin GPL v2 vagy újabb licenc alatt érhető el.

## Támogatás

Ha problémába ütközöl vagy kérdésed van, kérlek hozz létre egy issue-t a GitHub repository-ban.

## Közreműködés

A közreműködéseket szívesen fogadjuk! Kérlek:
1. Fork-old a repository-t
2. Hozz létre egy feature branch-et
3. Commit-old a változtatásaidat
4. Push-old a branch-et
5. Hozz létre egy Pull Request-et

---

**Fejlesztő**: Your Name  
**Utolsó frissítés**: 2024. január 