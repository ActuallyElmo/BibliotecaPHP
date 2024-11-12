# BibliotecaPHP
Aceasta este o aplicație web dezvoltată în PHP pentru gestionarea unei biblioteci online. Aplicația permite utilizatorilor să navigheze prin catalogul de cărți, să împrumute cărți și să gestioneze împrumuturile. Utilizatorii trebuie să fie autentificați pentru a accesa funcțiile avansate.

# Structura aplicației
Aplicația este organizată folosind arhitectura MVC (Model-View-Controller), care separă logica aplicației de interfața cu utilizatorul, pentru a facilita întreținerea și dezvoltarea pe termen lung.

- Modelul (Model): Definirea entităților și a metodelor de interacțiune cu baza de date. Fiecare entitate are o clasă corespunzătoare în PHP, unde sunt implementate operațiunile de tip CRUD (Create, Read, Update, Delete).
- Vederea (View): Pagini HTML care afișează conținutul pentru utilizatori, incluzând catalogul de cărți, formularul de autentificare și pagina de profil.
- Controllerul (Controller): Gestionarea logicii aplicației, inclusiv validarea datelor, autentificarea utilizatorilor și manipularea împrumuturilor.

# Roluri și Permisiuni
 ### Utilizator neautentificat: 
 Poate accesa catalogul de cărți și poate vizualiza detalii despre fiecare carte (ex. autor, gen, disponibilitate), dar nu poate împrumuta cărți sau accesa funcții avansate.
 ### Utilizator autentificat: 
 Are acces la funcții suplimentare, cum ar fi împrumutul și returnarea cărților. Utilizatorul poate vedea starea împrumuturilor proprii și își poate gestiona contul.
 ### Administrator: 
 Are acces la funcții avansate, cum ar fi adăugarea, modificarea și eliminarea cărților din catalog sau gestionarea conturilor utilizatorilor.
 
# Entitățile principale
 ### Carte: 
 Reprezintă o carte disponibilă în biblioteca online.
 Atribute: titlu, autor, gen, an publicare, disponibilitate.
 Relații: O carte poate fi împrumutată de mai multe ori; legătură cu mai multe înregistrări în entitatea Împrumut
 
 ### Utilizator: 
 Reprezintă o persoană cu un cont în aplicație, utilizată pentru autentificare și gestionarea împrumuturilor.
Atribute: nume, prenume, email, status de membru, parola.
Relații: Un utilizator poate avea mai multe împrumuturi asociate.

 ### Împrumut: 
 Face legătura între utilizator și carte, înregistrând detalii despre data împrumutului și, eventual, data returnării.
Atribute: id_carte, id_utilizator, data împrumut, data returnare.
Relații: Referințe la entitățile Carte și Utilizator.

# Procesele principale
- Autentificarea utilizatorilor: Permite utilizatorilor să se autentifice și să acceseze funcțiile personalizate.
- Înregistrarea utilizatorilor: Proces prin care un utilizator nou își creează un cont și devine utilizator autentificat.
- Navigarea și căutarea în catalogul de cărți: Utilizatorii (autentificați sau neautentificați) pot căuta și naviga prin catalogul de cărți, filtrând după titlu, autor, gen și disponibilitate.
- Împrumutul unei cărți: Utilizatorii autentificați pot solicita împrumutul unei cărți disponibile, iar aplicația va crea o înregistrare în tabelul Împrumut și va actualiza disponibilitatea cărții.
- Returnarea unei cărți: Utilizatorii autentificați pot returna o carte împrumutată, iar aplicația va marca împrumutul ca închis și va actualiza disponibilitatea cărții.

# Structura bazei de date
 ### Tabelul Carte:
- id_carte (cheie primară, identificator unic)
- titlu (numele cărții)
- autor (autorul cărții)
- gen (genul literar)
- an_publicare (anul publicării)
- disponibilitate (statutul de disponibilitate: TRUE/FALSE)
 ### Tabelul Utilizator:
- id_utilizator (cheie primară, identificator unic)
- nume și prenume (numele și prenumele utilizatorului)
- email (unic pentru fiecare utilizator, pentru autentificare)
- status_membru (poate fi "Standard", "Premium" sau "Admin")
- parola (criptată pentru securitate)
 ### Tabelul Împrumut:
- id_imprumut (cheie primară, identificator unic)
- id_carte (legătură cu cartea împrumutată, cheie străină)
- id_utilizator (legătură cu utilizatorul care împrumută, cheie străină)
- data_imprumut (data la care cartea a fost împrumutată)
- data_returnare (data la care cartea a fost returnată)
