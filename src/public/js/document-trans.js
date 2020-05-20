'use strict';

var DocumentTranslator = function (label) {

    return DocumentTranslator[DocumentTranslator.locale][label];
};

DocumentTranslator.setLocale = function (locale) {

    DocumentTranslator.locale = locale;
};

DocumentTranslator.ro = {
    declaration: 'DECLARAȚIE',
    recommendation: 'Măsură dispusă de D.S.P.:',
    send_to_hospital: 'trimitere spre spital',
    institutionalized_quarantine: 'carantinare instituționalizată',
    home_isolation: 'izolare la adresa declarată',
    agent_signature: 'Semnătură D.S.P.:',
    last_name: 'Nume:',
    first_name: 'Prenume:',
    identity_number: 'C.N.P.:',
    date_of_birth: 'Data nașterii:',
    year: '(an)',
    month: '(lună)',
    day: '(zi)',
    country_departure: 'Țara de plecare:',
    i_declare: 'Declar pe propria răspundere că:',
    first_question: '* am luat la cunoștință faptul că, pentru a preveni răspândirea pe teritoriul României a COVID-19, am obligația de a mă supune procedurilor de izolare/carantinare/internare, după caz;',
    second_question: '* pentru punerea în aplicare a măsurii izolării, după părăsirea perimetrului punctului de trecere a frontierei, mă voi deplasa pe cea mai scurtă rută la următoarea adresă:',
    agree_gdpr: '* sunt de acord ca datele cu caracter personal și informațiile furnizate să fie prelucrate de catre autoritățile competente;',
    agree_lies: '* am luat la cunoștință de prevederile art. 326 din Codul Penal cu privire la falsul în declarații și cele ale art. 352 din Codul Penal cu privire la zădărnicirea bolilor;',
    contact_at: 'Pe perioada șederii în România pot fi contactat la:',
    phone: 'Telefon:',
    signature: 'Semnătura',
    date: 'Data'
};

DocumentTranslator.en = {
    declaration: 'DECLARATION',
    recommendation: 'Measure disposed by the Department of Public Health (D.S.P):',
    send_to_hospital: 'hospital admission',
    institutionalized_quarantine: 'institutionalized quarantine',
    home_isolation: 'self-isolation at declared address',
    agent_signature: 'D.S.P. signature:',
    last_name: 'Name:',
    first_name: 'Surname:',
    identity_number: 'C.N.P.:',
    date_of_birth: 'Date of birth:',
    year: '(year)',
    month: '(month)',
    day: '(day)',
    country_departure: 'Country of departure:',
    i_declare: 'I hereby declare:',
    first_question: '* I am aware that, in order to prevent the spread of the SARS CoV-2 virus in Romania, I have the obligation to abide by the procedures of isolation/quarantine/hospital admission, as disposed.',
    second_question: '* In order to self-isolate, after leaving the premises of the border crossing point, I will follow the shortest route to the following address:',
    agree_gdpr: '* I agree to the use of my personal data and information found in this declaration by the competent authorities;',
    agree_lies: '* I acknowledge the provisions of the art. 326 in the Penal Code regarding false statements and art. 352 regarding thwarting disease control;',
    contact_at: 'During my stay in Romania I can be reached at:',
    phone: 'Phone number:',
    signature: 'Signature',
    date: 'Date'
};
