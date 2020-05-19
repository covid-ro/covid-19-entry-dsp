'use strict';

var Document = function () {

    this.doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
    });

    this.__ = DocumentTranslator;

    this.reset();
};

Document.prototype.reset = function () {

    this.doc.setFont('TimesNewRoman');
    this.doc.setFontStyle('normal');
    this.doc.setFontSize(10);
    this.doc.setTextColor(0, 0, 0);
};

Document.prototype.create = function (data, signature, qrcode, output) {

    this.__.setLocale(data.locale);

    this.draw();

    this.fill(data);

    this.addSignature(signature);

    this.addQrCode(qrcode);

    return this.doc.output(output);
};

Document.prototype.download = function (data, signature, qrcode) {

    let content = this.create(data, signature, qrcode, 'blob');
    let file = new Blob([content], { type: 'application/pdf' });
    let fileURL = URL.createObjectURL(file);
    var win = window.open();
    win.document.write('<iframe src="' + fileURL + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');

    // let content = this.create(data, signature, qrcode);
    // let iframe = "<iframe width='100%' height='100%' src='" + content + "'></iframe>";
    // let x = window.open();
    // x.document.open();
    // x.document.write(iframe);
    // x.document.close();

    // const el = document.createElement('a');

    // el.href = this.create(data, signature, qrcode);
    // el.target = '_blank';
    // el.download = this.getName(data);
    // el.click();
};

Document.prototype.getName = function(data) {

    let date = new Date();
    let year = date.getUTCFullYear();
    let month = date.getUTCMonth() + 1; // months from 1-12
    let day = date.getDate();

    if (month <= 9) {

        month = '0' + month;
    }

    return 'declaratie_' + data.code + '_' + year + month + day + '.pdf';
};

Document.prototype.draw = function () {

    this.doc.rect(10, 10, 190, 10);

    this.doc.setFontStyle('bold');
    this.doc.setFontSize(12);
    this.doc.text(this.__('declaration'), 105, 16, { align: 'center' });

    this.reset();

    // left column
    this.doc.text(this.__('last_name'), 10, 30);
    this.doc.line(22, 31, 100, 31);

    this.doc.text(this.__('first_name'), 10, 35);
    this.doc.line(25, 36, 100, 36);

    this.doc.text(this.__('gender'), 10, 40);
    this.doc.rect(55, 37.2, 3, 3);
    this.doc.text('M', 60, 40);
    this.doc.rect(70, 37.2, 3, 3);
    this.doc.text('F', 75, 40);

    this.doc.text(this.__('passport'), 10, 45);
    this.doc.text(this.__('series'), 35, 45);
    this.doc.line(45, 46, 60, 46);
    this.doc.text(this.__('number'), 67, 45);
    this.doc.line(80, 46, 100, 46);

    this.doc.text(this.__('date_of_birth'), 10, 50);
    this.doc.line(49, 51, 100, 51);

    this.doc.text(this.__('date_of_arrival_in_romania'), 10, 55);
    this.doc.line(49, 56, 100, 56);

    // right column
    this.doc.text(this.__('country_leave'), 105, 30);
    this.doc.line(140, 31, 200, 31);

    this.doc.text(this.__('locality_leave'), 105, 35);
    this.doc.line(122, 36, 200, 36);

    this.doc.text(this.__('date_leave'), 105, 40);
    this.doc.line(115, 41, 200, 41);

    // table
    this.doc.text(this.__('estimate_stay'), 10, 65);

    this.doc.setFontStyle('bold');

    // table - head
    this.doc.text(this.__('current_number'), 20, 72, { align: 'center' });
    this.doc.text(this.__('location_town'), 52, 72, { align: 'center' });
    this.doc.text(this.__('date_of_arrival'), 87, 72, { align: 'center' });
    this.doc.text(this.__('date_of_leave'), 113, 72, { align: 'center' });
    this.doc.text(this.__('full_address'), 160, 72, { align: 'center' });

    this.reset();

    // table - horizontal lines
    this.doc.line(10, 74, 200, 74);
    this.doc.line(10, 84, 200, 84);
    this.doc.line(10, 94, 200, 94);

    // contact
    this.doc.text(this.__('contact_me_at'), 10, 110);
    this.doc.text(this.__('phone_number'), 10, 115);
    this.doc.line(24, 116, 60, 116);
    this.doc.text(this.__('email'), 65, 115);
    this.doc.line(76, 116, 140, 116);

    // questions
    let quesiton1 = this.__('has_visited');
    this.doc.text(quesiton1, 10, 120, { maxWidth: 190 });
    this.doc.rect(10, 122.2, 3, 3);
    this.doc.text(this.__('yes'), 15, 125);
    this.doc.rect(25, 122.2, 3, 3);
    this.doc.text(this.__('no'), 30, 125);

    let quesiton2 = this.__('has_contacted');
    this.doc.text(quesiton2, 10, 130, { maxWidth: 190 });
    this.doc.rect(10, 137.2, 3, 3);
    this.doc.text(this.__('yes'), 15, 140);
    this.doc.rect(25, 137.2, 3, 3);
    this.doc.text(this.__('no'), 30, 140);

    let quesiton3 = this.__('is_hospitalized');
    this.doc.text(quesiton3, 10, 145, { maxWidth: 190 });
    this.doc.rect(10, 147.2, 3, 3);
    this.doc.text(this.__('yes'), 15, 150);
    this.doc.rect(25, 147.2, 3, 3);
    this.doc.text(this.__('no'), 30, 150);

    let quesiton4 = this.__('has_symptoms');
    this.doc.text(quesiton4, 10, 155, { maxWidth: 190 });

    this.doc.text('- ' + this.__('fever'), 10, 160);
    this.doc.rect(50, 157.2, 3, 3);
    this.doc.text(this.__('yes'), 55, 160);
    this.doc.rect(65, 157.2, 3, 3);
    this.doc.text(this.__('no'), 70, 160);

    this.doc.text('- ' + this.__('difficulty_swallow'), 10, 165);
    this.doc.rect(50, 162.2, 3, 3);
    this.doc.text(this.__('yes'), 55, 165);
    this.doc.rect(65, 162.2, 3, 3);
    this.doc.text(this.__('no'), 70, 165);

    this.doc.text('- ' + this.__('difficulty_breath'), 10, 170);
    this.doc.rect(50, 167.2, 3, 3);
    this.doc.text(this.__('yes'), 55, 170);
    this.doc.rect(65, 167.2, 3, 3);
    this.doc.text(this.__('no'), 70, 170);

    this.doc.text('- ' + this.__('intense_cough'), 10, 175);
    this.doc.rect(50, 172.2, 3, 3);
    this.doc.text(this.__('yes'), 55, 175);
    this.doc.rect(65, 172.2, 3, 3);
    this.doc.text(this.__('no'), 70, 175);

    // bigtext
    let text1 = this.__('important_notice');
    this.doc.text(text1, 10, 180, { maxWidth: 190 });

    let text2 = this.__('agreement_1');
    this.doc.text(text2, 10, 215, { maxWidth: 190 });

    let text3 = this.__('agreement_2');
    this.doc.text(text3, 10, 225, { maxWidth: 190 });

    let text4 = this.__('agreement_3');
    this.doc.text(text4, 10, 245, { maxWidth: 190 });

    let text5 = this.__('agreement_4');
    this.doc.text(text5, 10, 265, { maxWidth: 190 });

    // date
    this.doc.text(this.__('date_place'), 50, 275);
    this.doc.text(this.__('signature'), 140, 275);
};

Document.prototype.fill = function (data) {

    // top
    this.doc.text(data.locale === 'en' ? 'EN' : 'RO', 190, 16);


    // left column
    this.doc.text(data.lastName, 65, 30, { align: 'center' });
    this.doc.text(data.firstName, 65, 35, { align: 'center' });
    this.doc.text('X', data.sex === 'M' ? 56.5 : 71.5, 40, { align: 'center' });
    this.doc.text(data.idCardSeries, 53, 45, { align: 'center' });
    this.doc.text(data.idCardNumber, 90, 45, { align: 'center' });
    this.doc.text(data.birthday, 75, 50, { align: 'center' });
    this.doc.text(data.dateArrival, 75, 55, { align: 'center' });

    // right column
    this.doc.text(data.countryLeave, 170, 30, { align: 'center' });
    this.doc.text(data.localityLeave, 170, 35, { align: 'center' });
    this.doc.text(data.dateLeave, 170, 40, { align: 'center' });

    // table
    data.addresses.forEach(function (address, index) {

        let height = (index+1)*10;

        this.doc.text(index+1+'', 20, 70 + height, { align: 'center' });
        this.doc.text(address.locality, 52, 70 + height, { align: 'center' });
        this.doc.text(address.dateArrival, 87, 70 + height, { align: 'center' });
        this.doc.text(address.dateLeave, 113, 70 + height, { align: 'center' });
        this.doc.text(address.fullAddress, 160, 68 + height, { align: 'center', maxWidth: 65 });

    }.bind(this));

    // contact
    this.doc.text(data.phoneNumber, 43, 115, { align: 'center' });
    this.doc.text(data.emailAddress, 106, 115, { align: 'center' });

    // questions
    this.doc.text('X', data.answers.hasVisited ? 10 : 25, 125);
    this.doc.text('X', data.answers.hasContacted ? 10 : 25, 140);
    this.doc.text('X', data.answers.isHospitalized ? 10 : 25, 150);
    this.doc.text('X', data.answers.hasFever ? 50 : 65, 160);
    this.doc.text('X', data.answers.hasDifficultySwallow ? 50 : 65, 165);
    this.doc.text('X', data.answers.hasDifficultyBreath ? 50 : 65, 170);
    this.doc.text('X', data.answers.hasIntenseCough ? 50 : 65, 175);

    // bigtext
    this.doc.text(data.organization, data.locale === 'en' ? 97 : 159, 192, { align: 'center' });
    this.doc.text(data.visitedCountries.join(', '), data.locale === 'en' ? 135 : 125, 233, { align: 'center' });
    this.doc.text(data.borderCrossingPoint, data.locale === 'en' ? 50 : 75, 241, { align: 'center' });
    this.doc.text(data.destination, data.locale === 'en' ? 122 : 145, 249, { align: 'center' });
    this.doc.text(data.vehicle, data.locale === 'en' ? 56 : 103, 253, { align: 'center' });
    this.doc.text(data.route, 100, 257, { align: 'center' });
    this.doc.text(data.documentDate + ', ' + data.documentLocality, 59, 280, { align: 'center' });
};

Document.prototype.addSignature = function (signature) {
    if(signature.length > 0) {
        this.doc.addImage(signature, 'PNG', 155, 260, 40, 30);
    }
};

Document.prototype.addQrCode = function (qrcode) {

    this.doc.addImage(qrcode, 'PNG', 185, 45, 15, 15);
};
