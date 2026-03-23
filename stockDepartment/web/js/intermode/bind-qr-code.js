/**
 * Scan box barcode
 * */
$("#bindqrcodeform-box_barcode").on("keydown", function (e) {
  if (e.which == 13) {
    e.preventDefault();
    console.info("-bind-qr-code-form-box_barcode-");
    console.info("Value : " + $(this).val());

    var me = $(this),
      form = $("#bind-qr-code-process-form"),
      url = $(this).data("url");

    hideMessages();
    $("#bindqrcodeform-product_barcode").val("");
    $("#bindqrcodeform-our_product_barcode").val("");
    $("#bindqrcodeform-bind_qr_code").val("");
    $("#count-product-in-box").html(0);

    errorBase.setForm(form);
    me.focus().select();
    var data = form.serialize();
    $.post(
      url,
      data,
      function (result) {
        if (result.success == 0) {
          errorBase.eachShow(result.errors);
          $("#count-product-in-box").html(0);
          me.focus().select();
        } else {
          errorBase.hidden();
          $("#bindqrcodeform-product_barcode").focus().select();
          $("#count-product-in-box").html(result.countProductInBox);
        }
      },
      "json"
    ).fail(function (xhr, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      me.focus().select();
    });
  }
});

$("#bindqrcodeform-product_barcode").on("keydown", function (e) {
  var me = $(this);
  if (e.which == 13) {
    e.preventDefault();
    console.info("-bindqrcodeform-product_barcode-");
    console.info("Value : " + $(this).val());

    var url = me.data("url"),
      form = $("#bind-qr-code-process-form");

    hideMessages();
    $("#bindqrcodeform-our_product_barcode").val("");
    $("#bindqrcodeform-bind_qr_code").val("");

    errorBase.setForm(form);
    me.focus().select();

    $.post(
      url,
      form.serialize(),
      function (result) {
        if (result.success == 0) {
          errorBase.eachShow(result.errors);
          me.focus().select();
        } else {
          errorBase.hidden();
          $("#bindqrcodeform-our_product_barcode").focus().select();
        }
      },
      "json"
    ).fail(function (xhr, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      me.focus().select();
    });
  }
});

$("#bindqrcodeform-our_product_barcode").on("keydown", function (e) {
  var me = $(this);
  if (e.which == 13) {
    e.preventDefault();
    console.info("-bindqrcodeform-our_product_barcode-");
    console.info("Value : " + $(this).val());

    var url = me.data("url"),
      form = $("#bind-qr-code-process-form");

    hideMessages();
    $("#bindqrcodeform-bind_qr_code").val("");

    errorBase.setForm(form);
    me.focus().select();

    $.post(
      url,
      form.serialize(),
      function (result) {
        if (result.success == 0) {
          errorBase.eachShow(result.errors);
          me.focus().select();
        } else {
          errorBase.hidden();
          $("#bindqrcodeform-bind_qr_code").focus().select();
        }
      },
      "json"
    ).fail(function (xhr, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      me.focus().select();
    });
  }
});

$("#bindqrcodeform-bind_qr_code").on("keydown", function (e) {
  var me = $(this);
  if (e.which == 13) {
    e.preventDefault();
    console.info("-bindqrcodeform-bind_qr_code-");
    console.info("Value : " + $(this).val());

    var url = me.data("url"),
      form = $("#bind-qr-code-process-form");
    hideMessages();

    errorBase.setForm(form);
    me.focus().select();

    $.post(
      url,
      form.serialize(),
      function (result) {
        if (result.success == 0) {
          errorBase.eachShow(result.errors);
          me.focus().select();
          hideMessages();
          showMessage(result.message, "warning");
        } else {
		  $("#bindqrcodeform-product_barcode").focus().select();
          errorBase.hidden();
          showMessage(result.message, "success");
        }
      },
      "json"
    ).fail(function (xhr, textStatus, errorThrown) {
      console.error("Error:", errorThrown);
      me.focus().select();
    });
  }
});

function hideMessages() {
  $(".alert-success").fadeOut(400, function () {
    $(this).remove();
  });
  $(".alert-warning").fadeOut(400, function () {
    $(this).remove();
  });
}

function showMessage(message, type) {
  $("#messages-container").html(
    '<div class="alert alert-' +
      type +
      ' alert-dismissible" role="alert">' +
      message +
      "</div>"
  );
}