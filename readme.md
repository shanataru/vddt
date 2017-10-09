# Vizualizace databáze dynamických textur

Na čem se pracuje aktuálně:
 * Jsou rozkopaná CSSka!!!!! Pokud se vám něco nevykrsluje správně, tak klidně vytvořte isuue, ale zatím jsme v průběhu přechodu na novější verzi cssek (smutně pokud to vypadá všechny jako dřív, tak je to už ta nová verze a funguje to)

Co se navrhuje/vymýšlí:
 * Vyhledávání - viz issue #3, hledáme nápady


## How to git & ftp

Používáme git a ftp-git, typické použití před uploadem na ftp:
```bash
#commit lokální změny
git commit -m "něco něco"
#pull změny na githubu, lokání změny se už neztratí
#a mergnou se s tím co je nahrané, neměly by být konflikty většinou,
#kdyžtak pořešit přes git add
git pull 
#mám hotovo, můžu udělat
git push
#a konečně to nahraju na ftp
git ftp push
```
## Changelog
Co je nového a co se změnilo:

### 3-10-2017
* Stěhování na github

### 1-10-2017
* Mazání masky, doopravených pár drobností z dřívěji
* Nové nahrávání binárek, úprava binárek.

### 29-9-2017
* Formulář na změny údajů v profilu - jméno & titul, email
* Úprava masky, přidávání a odebírání materiálů v úpravě

### 28-9-2017
* Oprava chyb v gridech (řazení, neseděla jména z databáze a v gridech) - **Tato chyba se pravděpodobně bude ještě vyskytovat, není zkontrolovaná asi polovina gridů.**
* Nahrávání masek, zatím je spojení s materiály řešeno tak že se tam napíšou idčka za sebe s čárkou a děj se vůle boží, **tohle chce předělat.**
* Opravené další malé věci v gridech, opět chybné namespace, taky něco v template materiálu.
* Přidané metody a konstanty do Model.php, budou se hodit ještě při nahrávání jiných věcí.
* Dodělaná aktivace účtu s odesíláním emailu.
* Dodělaná změna hesla z přihlašovací stránky.

### 27-9-2017
* Zmenšování náhledů na max velikost 800x600 u fotografií, videa stále chybí
* Opravené počítání položek podle viditelných na některých místech (ne všech)
* Přepsané Material.php, nahrávání a upravování, již funguje přidávání i mazání tagů.
* V detailu materiálu se mění obsah nabídky podle vlastníka, ošetření přístupu pro mazání a upravování (zatím hází vyjímku když se o to pokusí někdo jiný než autor)
* Rozdělané rozesílání aktivačních mailů po registraci (nedodělané, řeší se)
* Dodělané mazání materiálů i s náhledy. **Momentálně to funguje tak, že smažeš materiál a masky a tagy zůstanou, jen se rozpadne vazba mezi nima.**
* Upravená tlačítka a no-data dialogy na stránce detailu materiálu, opravené počítání masek.

### 27-9-2017
* Přidána podpora ajaxu
* Předělané nahrávání materiálů
* Rozpracované mazání materiálů (momentálně nefuguje)
* Vyčištění celé datové vrstvy (vymazání duplicitních procedur, přesun těchto částí atp.)

### 25-9-2017
* Nové ikony, přepracované tabulky