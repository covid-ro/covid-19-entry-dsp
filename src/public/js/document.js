'use strict';

var Document = function () {

    this.doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: 'a5'
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

    //this.addSignature(signature);

    this.addQrCode(qrcode);

    return this.doc.output(output, this.getName(data));
};

Document.prototype.download = function (data, signature, qrcode, output) {

    this.create(data, signature, qrcode, 'save');
};

Document.prototype.preview = function (data, signature, qrcode) {

    // Method 1
    let content = this.create(data, signature, qrcode, 'blob');
    let file = new Blob([content], { type: 'application/pdf' });
    let fileURL = URL.createObjectURL(file);
    var win = window.open();
    win.document.write('<iframe src="' + fileURL + '" frameborder="0" style="border:0; top:0px; left:0px; bottom:0px; right:0px; width:100%; height:100%;" allowfullscreen></iframe>');

    // Method 2
    // let content = this.create(data, signature, qrcode);
    // let iframe = "<iframe width='100%' height='100%' src='" + content + "'></iframe>";
    // let x = window.open();
    // x.document.open();
    // x.document.write(iframe);
    // x.document.close();

    // Method 3
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

    this.doc.setFontStyle('bold');
    this.doc.setFontSize(12);
    this.doc.text(this.__('declaration'), 105, 15, { align: 'center' });

    this.reset();

    // first rectangle
    this.doc.rect(10, 25, 190, 16);

    this.doc.text(this.__('recommendation'), 12, 30);

    this.doc.rect(12, 33, 3, 3);
    this.doc.text(this.__('send_to_hospital'), 17, 35.5);

    this.doc.rect(52, 33, 3, 3);
    this.doc.text(this.__('institutionalized_quarantine'), 57, 35.5);

    this.doc.rect(102, 33, 3, 3);
    this.doc.text(this.__('home_isolation'), 107, 35.5);

    this.doc.line(160, 25, 160, 41);
    this.doc.text(this.__('agent_signature'), 162, 30);

    // second rectangle
    this.doc.rect(10, 45, 190, 20);

    this.doc.text(this.__('last_name'), 12, 50);
    this.doc.line(28, 50.5, 80, 50.5);

    this.doc.text(this.__('first_name'), 90, 50);
    this.doc.line(106, 50.5, 197, 50.5);

    this.doc.text(this.__('identity_number'), 12, 55);
    this.doc.line(28, 55.5, 84, 55.5);

    this.doc.text(this.__('date_of_birth'), 92, 55);

    this.doc.text(this.__('year'), 130, 55);
    this.doc.line(115, 55.5, 129, 55.5);

    this.doc.text(this.__('month'), 160, 55);
    this.doc.line(145, 55.5, 159, 55.5);

    this.doc.text(this.__('day'), 190, 55);
    this.doc.line(175, 55.5, 189, 55.5);

    this.doc.text(this.__('country_departure'), 12, 60);
    this.doc.line(45, 60.5, 90, 60.5);

    // free text
    this.doc.text(this.__('i_declare'), 10, 70);

    this.doc.text(this.__('first_question'), 10, 75, { maxWidth: 190 });

    this.doc.text(this.__('second_question'), 10, 85, { maxWidth: 190 });
    this.doc.line(58, 89.8, 200, 89.8);

    this.doc.text(this.__('agree_gdpr'), 10, 95, { maxWidth: 190 });

    this.doc.text(this.__('agree_lies'), 10, 100, { maxWidth: 190 });

    // contact
    this.doc.text(this.__('contact_at'), 10, 110);

    this.doc.text(this.__('phone'), 10, 115);
    this.doc.line(35, 115.5, 70, 115.5);

    // footer
    this.doc.text(this.__('signature'), 30, 125);
    this.doc.text(this.__('date'), 167, 125);
};

Document.prototype.fill = function (data) {

    // first rectangle
    // if (data.measure.hospital) {
    //     this.doc.text('X', 12, 35.5);
    // }
    // if (data.measure.quarantine) {
    //     this.doc.text('X', 52, 35.5);
    // }
    // if (data.measure.isolation) {
    //     this.doc.text('X', 102, 35.5);
    // }

    // second rectangle
    this.doc.text(data.lastName, 55, 50, { align: 'center' });
    this.doc.text(data.firstName, 150, 50, { align: 'center' });
    this.doc.text(data.idCardNumber, 55, 55, { align: 'center' });
    this.doc.text(data.dateOfBirth.year, 122, 55, { align: 'center' });
    this.doc.text(data.dateOfBirth.month, 152, 55, { align: 'center' });
    this.doc.text(data.dateOfBirth.day, 182, 55, { align: 'center' });
    this.doc.text(data.countryDeparture, 68, 60, { align: 'center' });

    // free text
    this.doc.text(data.destinationAddress, 60, 89.2);
    this.doc.text(data.phoneNumber, 52, 115, { align: 'center' });

    // footer
    this.doc.text(data.documentDate, 170, 130, { align: 'center' });

};

Document.prototype.addSignature = function (signature) {

    if(!signature) {
        return;
    }

    this.doc.addImage(signature, 'PNG', 20, 120, 40, 30);
};

Document.prototype.addQrCode = function (qrcode) {

    if (!qrcode) {
        return;
    }

    this.doc.addImage(qrcode, 'PNG', 185, 8, 15, 15);
};
