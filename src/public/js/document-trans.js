'use strict';

var DocumentTranslator = function (label) {

    return DocumentTranslator[DocumentTranslator.locale][label];
};

DocumentTranslator.setLocale = function (locale) {

    DocumentTranslator.locale = locale;
};

DocumentTranslator.ro = {
    declaration: 'DECLARAȚIE',
    first_name: 'Prenume',
    last_name: 'Nume',
    gender: 'Sex',
    passport: 'Pasaport/C.I.',
    series: 'Serie',
    number: 'Numar',
    date_of_birth: 'Data nașterii (zi/lună/an)',
    date_of_arrival_in_romania: 'Data sosirii în România',
    country_leave: 'Țara din care ați plecat',
    locality_leave: 'Localitate',
    date_leave: 'Data',
    estimate_stay: 'Estimez că voi rămâne în Romania mai mult de 24 de ore la următoarele adrese:',
    current_number: 'Nr. Crt.',
    location_town: 'Locația (oraș)',
    date_of_arrival: 'Data sosirii',
    date_of_leave: 'Data plecării',
    full_address: 'Adresa completă',
    contact_me_at: 'Pe perioada șederii în Romania, pot fi contactat la:',
    phone_number: 'Nr. Tel.',
    email: 'Email',
    has_visited: '1. Ați locuit/vizitat zone în care se aflau persoane suferinde din cauza infecției cu noul coronavirus (COVID-19)?',
    has_contacted: '2. Ați venit în contact direct cu persoane suferinde din cauza infecției cu noul coronavirus (COVID-19) la serviciu, în vecinătatea locuinței sau vizitând unități medicale ori alte genuri de locuri în ultimele 14 zile?',
    is_hospitalized: '3. Ați fost spitalizat în ultimele trei săptămâni?',
    has_symptoms: '4. Ați avut una sau mai multe dintre urmatoarele simptome?',
    fever: 'Febră',
    difficulty_swallow: 'Dificultatea de a înghiți',
    difficulty_breath: 'Dificultatea de a respira',
    intense_cough: 'Tuse intensă',
    yes: 'Da',
    no: 'Nu',
    important_notice: 'Aviz important și în acord: În contextul evoluțiilor înregistrate începând cu ianuarie 2020 în legatură cu infecția cu noul coronavirus COVID-19, pentru a putea rămâne în România, toți pasagerii din sau care au călătorit recent în China, Italia, Coreea de Sud, Iran, sunt obligați să completeze chestionarul de mai sus. Vă rugăm să rețineți că datele și informațiile furnizate aici sunt solicitate pentru colectare și pentru prelucrare de către Direcția de Sănătate Publică Județeană _____________________________________________. Datele și informațiile solicitate și colectate sunt prelucrate în conformitate cu prevederile Regulamentului nr. 679/2016 privind protecția persoanelor fizice în ceea ce privește prelucrarea datelor cu caracter personal și libera circulație a acestor date, cu respectarea strictă a principiilor legate de drepturile fundamentale. Persoanele ale căror date sunt prelucrate beneficiază de dreptul de a-și exercita drepturile de modificare, intervenție și opoziție printr-o cerere semnată, datată și scrisă adresată operatorului de date.',
    agreement_1: '* Sunt conștient că un refuz de a completa chestionarul poate provoca refuzul intrării mele pe teritoriul României, în scopul eliminării eventualelor amenințări la adresa sănătății publice a României.',
    agreement_2: '* Cunoscând prevederile art. 326 din Codul Penal cu privire la falsul în declarații și art. 352 din Codul Penal cu privire la zădărnicirea combaterii bolilor, declar prin prezenta, pe propria răspundere, că am sosit pe teritoriul României plecând din țara de origine, cu tranzitarea teritoriului următoarelor țări: _________________________________________________________________ și că voi urma indicațiile personalului medical care mi-au fost aduse la cunoștiință pe timpul efectuării controlului de frontieră în punctul de trecere a frontierei _____________________________________________.',
    agreement_3: '* Declar pe propria răspundere faptul că, pentru a preveni răspândirea pe teritoriul României a virusului COVID-19, după părăsirea perimetrului punctului de trecere a frontierei mă voi deplasa la ________________________________________________________, pentru auto-izolare sau plasare în carantină, folosind _____________________ urmând traseul următor: ___________________________________________________________________________________________________________.',
    agreement_4: '* Sunt de acord că informațiile furnizate pot fi consultate și prelucrate de către autoritățile competente.',
    date_place: 'Data și locul',
    signature: 'Semnătura'
};

DocumentTranslator.en = {
    declaration: 'DECLARATION',
    first_name: 'Surname',
    last_name: 'Name',
    gender: 'Gender',
    passport: 'Pasaport/I.D.',
    series: 'Series',
    number: 'Number',
    date_of_birth: 'Date of birth (d/m/y)',
    date_of_arrival_in_romania: 'Date of arrival in România',
    country_leave: 'The country of departure',
    locality_leave: 'City/Town',
    date_leave: 'Date',
    estimate_stay: 'I estimate that I will be staying in Romania for more than 24 hours at the following addresses:',
    current_number: 'No.',
    location_town: 'Location (town)',
    date_of_arrival: 'Date of arrival',
    date_of_leave: 'Date of departure',
    full_address: 'Complete address',
    contact_me_at: 'During my stay/travel to Romania I can be reached at:',
    phone_number: 'Phone',
    email: 'Email',
    has_visited: 'Have you ever lived in/visited areas where there were persons suffering from the infection with the new coronavirus (COVID-19)?',
    has_contacted: 'Have you come in direct contact with persons suffering from the infection with the new coronavirus (COVID-19) at work, nearby your residence or when visiting medical units or other type of places in the last 14 days?',
    is_hospitalized: 'Have you ever been hospitalized during the last three weeks?',
    has_symptoms: 'Have you had one or more of the following symptoms?',
    fever: 'Fever',
    difficulty_swallow: 'Difficulty in swallowing',
    difficulty_breath: 'Difficulty in breathing',
    intense_cough: 'Intense coughing',
    yes: 'Yes',
    no: 'No',
    important_notice: 'Important notice and agreement: In the context of the recorded developments starting January 2020 regarding the infection with the new coronavirus COVID-19, in order to remain in Romania, all passengers from or who have recently traveled to China, Italy, South Korea, Iran are required to fill in the above questionnaire. Please not that the data and information provided here are required for collection and processing by the _____________________________________________ County public Health Department. The required and collected data and information are processed in accordance with the provisions of Regulation no. 678/2016 on the protection of natural persons with regard to the processing of personal data and on the free movement of such data, with strict compliance of the principles related to fundamental rights. The persons whose data are processed have the right to modify, intervene and oppose through written, dated and signed request addressed to the data operator.',
    agreement_1: '* I am aware that the refusal to fill in the questionnaire may cause the refusal of my entry in Romania, in order to eliminate any possible threats to the public health of Romania.',
    agreement_2: 'Acknowledging the provisions of art. 326 of the Criminal Code regarding false statements and art. 352 regarding the thwarting disease control, I hereby declare, on my own responsibility, that I have arrived on the territory of Romania leaving the country of origin, transiting the territory of the following countries: _________________________________________________________________ and that I will follow the instructions provided by the medical personnel during the border control at the border crossing point _____________________________________________.',
    agreement_3: '* I declare on my own responsability that, in order to prevent the spread of COVID-19 virus on the territory of Romania, after leaving the border crossing point area I will travel to ________________________________________________________ for self-isolation or quarantine using _____________________ on the following route ___________________________________________________________________________________________________________.',
    agreement_4: '* I agree that the provided information can be consulted and processed by competent authorities.',
    date_place: 'Date and place',
    signature: 'Signature'
};
