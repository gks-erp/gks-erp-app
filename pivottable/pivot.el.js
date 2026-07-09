(function() {
  var callWithJQuery;

  callWithJQuery = function(pivotModule) {
    if (typeof exports === "object" && typeof module === "object") {
      return pivotModule(require("jquery"));
    } else if (typeof define === "function" && define.amd) {
      return define(["jquery"], pivotModule);
    } else {
      return pivotModule(jQuery);
    }
  };

  callWithJQuery(function($) {
    var frFmt, frFmtInt, frFmtPct, nf, tpl;
    nf = $.pivotUtilities.numberFormat;
    tpl = $.pivotUtilities.aggregatorTemplates;
    frFmt = nf({
      thousandsSep: ".",
      decimalSep: ","
    });
    frFmtInt = nf({
      digitsAfterDecimal: 0,
      thousandsSep: ".",
      decimalSep: ","
    });
    frFmtPct = nf({
      digitsAfterDecimal: 1,
      scaler: 100,
      suffix: "%",
      thousandsSep: ".",
      decimalSep: ","
    });
    return $.pivotUtilities.locales.el = {
      localeStrings: {
        renderError: "Παρουσιάστηκε σφάλμα κατά τη δημιουργία του πίνακα.",
        computeError: "Παρουσιάστηκε ένα σφάλμα υπολογισμού του πίνακα.",
        uiRenderError: "Παρουσιάστηκε ένα σφάλμα κατά τη διάρκεια του σχεδιασμού διεπαφής του συγκεντρωτικού πίνακα.",
        selectAll: "Επιλογή όλων",
        selectNone: "Αποεπιλογή όλων",
        tooMany: "(Πάρα πολλές τιμές για να εμφανιστούν)",
        filterResults: "Φίλτρο τιμών",
        totals: "Σύνολα",
        vs: "VS",
        by: "από",
        apply: "Εφαρμογή",
        cancel: "Άκυρο"
      },
      aggregators: {
        "Αριθμός": tpl.count(frFmtInt),
        "Αριθμός με μοναδικές τιμές": tpl.countUnique(frFmtInt),
        "Λίστα με μοναδικές τιμές": tpl.listUnique(", "),
        "Άθροισμα": tpl.sum(frFmt),
        "Ολόκληρο το ποσό": tpl.sum(frFmtInt),
        "Μέσος Όρος": tpl.average(frFmt),
        "Ελάχιστο": tpl.min(frFmt),
        "Μέγιστο": tpl.max(frFmt),
        "Σχετικός λόγος": tpl.sumOverSum(frFmt),
        "Άνω του ορίου 80%": tpl.sumOverSumBound80(true, frFmt),
        "Κάτω του ορίου 80%": tpl.sumOverSumBound80(false, frFmt),
        "Ανάλογο με το συνολικό ποσό": tpl.fractionOf(tpl.sum(), "total", frFmtPct),
        "Άθροισμα ανάλογη προς τη γραμμή": tpl.fractionOf(tpl.sum(), "row", frFmtPct),
        "Άθροισμα ανάλογη προς τη στήλη": tpl.fractionOf(tpl.sum(), "col", frFmtPct),
        "Ανάλογο με το συνολικό αριθμό": tpl.fractionOf(tpl.count(), "total", frFmtPct),
        "Αριθμός ανάλογη προς τη γραμμή": tpl.fractionOf(tpl.count(), "row", frFmtPct),
        "Αριθμός ανάλογη με στήλη": tpl.fractionOf(tpl.count(), "col", frFmtPct)
      },
      renderers: {
        "Πίνακας": $.pivotUtilities.renderers["Table"],
        "Πίνακας με γραφικά": $.pivotUtilities.renderers["Table Barchart"],
        "Χάρτης θερμότητας": $.pivotUtilities.renderers["Heatmap"],
        "Χάρτης θερμότητας σε σειρές": $.pivotUtilities.renderers["Row Heatmap"],
        "Χάρτης θερμότητας σε στήλες": $.pivotUtilities.renderers["Col Heatmap"]
      }
    };
  });

}).call(this);

