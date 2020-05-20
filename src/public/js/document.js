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

    return this.doc.output(output, this.getName(data));
};

Document.prototype.download = function (data, signature, qrcode, output) {

    this.create(data, signature, qrcode, 'save');
};

Document.prototype.preview = function (data, signature, qrcode) {

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

    this.doc.setFontStyle('bold');
    this.doc.setFontSize(12);
    this.doc.text(this.__('declaration'), 105, 16, { align: 'center' });

    this.reset();

    // first rectangle
    this.doc.rect(10, 45, 190, 16);

    this.doc.text(this.__('recommendation'), 12, 50);

    this.doc.rect(12, 53, 3, 3);
    this.doc.text(this.__('send_to_hospital'), 17, 55.5);

    this.doc.rect(52, 53, 3, 3);
    this.doc.text(this.__('institutionalized_quarantine'), 57, 55.5);

    this.doc.rect(102, 53, 3, 3);
    this.doc.text(this.__('home_isolation'), 107, 55.5);

    this.doc.line(160, 45, 160, 61);
    this.doc.text(this.__('agent_signature'), 162, 50);

    // second rectangle
    this.doc.rect(10, 65, 190, 20);

    this.doc.text(this.__('last_name'), 12, 70);
    this.doc.line(28, 70.5, 80, 70.5);

    this.doc.text(this.__('first_name'), 90, 70);
    this.doc.line(106, 70.5, 197, 70.5);

    this.doc.text(this.__('identity_number'), 12, 75);
    this.doc.line(28, 75.5, 84, 75.5);

    this.doc.text(this.__('date_of_birth'), 92, 75);

    this.doc.text(this.__('year'), 130, 75);
    this.doc.line(115, 75.5, 129, 75.5);

    this.doc.text(this.__('month'), 160, 75);
    this.doc.line(145, 75.5, 159, 75.5);

    this.doc.text(this.__('day'), 190, 75);
    this.doc.line(175, 75.5, 189, 75.5);

    this.doc.text(this.__('country_departure'), 12, 80);
    this.doc.line(45, 80.5, 90, 80.5);

    // free text
    this.doc.text(this.__('i_declare'), 10, 100);

    this.doc.text(this.__('first_question'), 10, 105, { maxWidth: 190 });

    this.doc.text(this.__('second_question'), 10, 115, { maxWidth: 190 });
    this.doc.line(58, 119.8, 200, 119.8);

    this.doc.text(this.__('agree_gdpr'), 10, 125, { maxWidth: 190 });

    this.doc.text(this.__('agree_lies'), 10, 130, { maxWidth: 190 });

    // contact
    this.doc.text(this.__('contact_at'), 10, 140);

    this.doc.text(this.__('phone'), 10, 145);
    this.doc.line(35, 145.5, 70, 145.5);

    // footer
    this.doc.text(this.__('signature'), 30, 200);
    this.doc.text(this.__('date'), 167, 200);
};

Document.prototype.fill = function (data) {

    // first rectangle
    if (data.measure.hospital) {
        this.doc.text('X', 12, 55.5);
    }
    if (data.measure.quarantine) {
        this.doc.text('X', 52, 55.5);
    }
    if (data.measure.isolation) {
        this.doc.text('X', 102, 55.5);
    }

    // second rectangle
    this.doc.text(data.lastName, 55, 70, { align: 'center' });
    this.doc.text(data.firstName, 150, 70, { align: 'center' });
    this.doc.text(data.idCardNumber, 55, 75, { align: 'center' });
    this.doc.text(data.dateOfBirth.year, 122, 75, { align: 'center' });
    this.doc.text(data.dateOfBirth.month, 152, 75, { align: 'center' });
    this.doc.text(data.dateOfBirth.day, 182, 75, { align: 'center' });
    this.doc.text(data.countryDeparture, 68, 80, { align: 'center' });

    // free text
    this.doc.text(data.destinationAddress, 60, 119.2);
    this.doc.text(data.phoneNumber, 52, 145, { align: 'center' });

    // footer
    this.doc.text(data.documentDate, 170, 205, { align: 'center' });

};

Document.prototype.addSignature = function (signature) {

    if(!signature) {
        return;
    }

    this.doc.addImage(signature, 'PNG', 20, 205, 40, 30);
};

Document.prototype.addQrCode = function (qrcode) {

    if (!qrcode) {
        return;
    }

    this.doc.addImage(qrcode, 'PNG', 185, 8, 15, 15);
};
