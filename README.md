# Melisa-Bujupi-Sistemi-p-r-menaxhimin-e-Kurseve-dhe-Trajnimeve

Raporti Teknik 1 – Analiza, Kërkesat dhe Dizajni Teknik 

 Përshkrimi i problemit 
Ideja e projektit është zhvillimi i një sistemi për menaxhimin e kurseve dhe trajnimeve, i cili do 
të lehtësojë organizimin e kurseve, regjistrimin e pjesëmarrësve dhe menaxhimin e informatave 
nga administratorët dhe trajnerët. Në shumë raste, këto procese realizohen në mënyrë 
manuale, duke shkaktuar humbje të të dhënave, vështirësi në organizim dhe vonesa në 
komunikim. Sistemi do të mundësojë ruajtjen dhe menaxhimin e të dhënave në një vend të 
vetëm, duke e bërë procesin më të thjeshtë dhe më efikas. 

 Qëllimi i projektit 
Qëllimi i këtij projekti është krijimi i një aplikacioni që mundëson menaxhimin e kurseve dhe 
trajnimeve, duke përfshirë regjistrimin e përdoruesve, publikimin e kurseve, regjistrimin e 
pjesëmarrësve dhe administrimin e informatave për trajnerët. Sistemi synon të përmirësojë 
organizimin e trajnimeve dhe të ofrojë qasje më të lehtë në informatat e nevojshme. 

 Përdoruesit e sistemit 
Sistemi do të ketë tri kategori kryesore të përdoruesve: 
- Administratori: Menaxhon përdoruesit, kurset, trajnerët dhe regjistrimet. 
- Trajneri: Shikon kurset e caktuara dhe listën e pjesëmarrësve. 
- Pjesëmarrësi: Shfleton kurset e disponueshme dhe regjistrohet në kursin që dëshiron.

 Funksionalitetet kryesore 
-Sistemi do të përfshijë këto funksionalitete: 
 -Regjistrimi dhe kyçja e përdoruesve në sistem. 
 -Shtimi, ndryshimi dhe fshirja e kurseve. 
 Menaxhimi i informatave për trajnerët. 
- Regjistrimi i pjesëmarrësve në kurse. 
-Shfaqja e listës së kurseve dhe detajeve të tyre. 
 Shikimi i regjistrimeve dhe menaxhimi i tyre nga administratori.

 Kërkesat e sistemit 
 Kërkesat funksionale 
- Sistemi duhet të mundësojë kyçjen e përdoruesve me kredenciale. 
- Administratori duhet të mund të shtojë, përditësojë dhe fshijë kurse. 
- Pjesëmarrësi duhet të mund të shikojë kurset e disponueshme dhe të regjistrohet. 
- Trajneri duhet të mund të shikojë kurset dhe pjesëmarrësit përkatës. 
- Sistemi duhet t'i ruajë të dhënat e përdoruesve, kurseve dhe regjistrimeve në databazë. 
 Kërkesat jofunksionale 
- Sistemi duhet të jetë i thjeshtë dhe i lehtë për përdorim. 
- Të dhënat duhet të ruhen në mënyrë të organizuar dhe të sigurt. 
- Sistemi duhet të kontrollojë të dhënat e futura nga përdoruesit. 
- Faqet duhet të përshtaten për përdorim në kompjuter dhe pajisje mobile. 
- Qasja në funksionet administrative duhet të kufizohet vetëm për përdoruesit e 
autorizuar.

 Teknologjitë e planifikuara 
Për zhvillimin e projektit planifikohet të përdoren këto teknologji: 
- HTML dhe CSS – për strukturën dhe dizajnin e ndërfaqes. 
- JavaScript – për interaktivitetin dhe validimin e formularëve. 
- PHP – për logjikën e aplikacionit dhe komunikimin me databazën. 
- MySQL – për ruajtjen dhe menaxhimin e të dhënave. 
- Git dhe GitHub – për ruajtjen e kodit dhe përcjelljen e progresit të projektit. 
Këto teknologji janë të përshtatshme për ndërtimin e një aplikacioni web me databazë 
relacionale.

 Arkitektura e sistemit 
Sistemi do të organizohet në tri pjesë kryesore: ndërfaqja e përdoruesit, logjika e aplikacionit 
dhe databaza.
Përdoruesi:Admin/Trajner/Student
Frontend-i: HTML/CSS/JS
Backend-i:PHP
MySQL:Database
Përdoruesi ndërvepron me ndërfaqen e aplikacionit, kërkesat përpunohen nga PHP-ja dhe të 
dhënat ruhen ose lexohen nga databaza MySQL. 

 Modeli fillestar i databazës 
Databaza do të përmbajë tabelat kryesore në vijim: 
- Users: User_ID, Name, Email, Password, Role. 
- Courses: Course_ID, Title, Description, Start_Date, End_Date, Price, Trainer_ID. 
- Trainers: Trainer_ID, Name, Email, Specialization. 
- Enrollments: Enrollment_ID, User_ID, Course_ID, Enrollment_Date, Status. 
Çdo tabelë do të ketë një çelës primar (Primary Key). Tabela Courses do të lidhet me Trainers 
përmes Trainer_ID, ndërsa tabela Enrollments do të lidhet me Users dhe Courses përmes 
çelësave të huaj (Foreign Keys). 
Një trajner mund të mbajë disa kurse, ndërsa një pjesëmarrës mund të regjistrohet në disa 
kurse. Tabela Enrollments do të mundësojë menaxhimin e lidhjes ndërmjet pjesëmarrësve dhe 
kurseve.

 Plani i punës 
Gjatë zhvillimit të projektit planifikohet të realizohen këto faza: 
- Analiza e problemit dhe përcaktimi i kërkesave të sistemit. 
- Dizajnimi i arkitekturës dhe modelit fillestar të databazës. 
- Krijimi i databazës dhe implementimi i funksionaliteteve kryesore në backend. 
- Zhvillimi i ndërfaqes dhe integrimi me databazën. 
- Testimi i funksionaliteteve, rregullimi i gabimeve dhe përgatitja e versionit final. 
Deri në raportin e dytë synohet të krijohet databaza fillestare, të ndërtohen tabelat kryesore 
dhe të implementohen funksionet bazë për ruajtjen, leximin dhe ndryshimin e të dhënave.

Përfundimi
Sistemi i Menaxhimit të Kurseve dhe Trajnimeve synon të ofrojë një zgjidhje praktike për 
organizimin e kurseve, menaxhimin e trajnerëve dhe regjistrimin e pjesëmarrësve. Përmes këtij 
projekti do të ndërtohet një aplikacion web me databazë relacionale, duke zbatuar konceptet e 
analizës së kërkesave, dizajnit teknik dhe zhvillimit të softuerit. Ky raport paraqet bazën fillestare të projektit, ndërsa në fazat e ardhshme do të realizohen dhe testohen funksionalitetet e planifikuara. 
