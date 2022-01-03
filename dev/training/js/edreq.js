$(".lierror").hide();
$("#xferdiv").hide();
$("#lineitemmsg").hide();
$("#lineiteminfo").hide();
$("#confirmbtn").prop('disabled', false);
var linesreviewed;

tippy('#sugvhelp', {
  content: 'Start typing vendor name or number, then select from list',
});

tippy('#atthelp', {
  content: 'Drop a file here or click the paperclip to select one',
});

if(reqid != 0) {
  getlineitems(reqid);
  getatts(reqid);
}
function formvalid(reqtype) {
  var fv = true;
  $.each($(".val-always, .val-submit"), function() {
    var elem = $(this)[0];
    if(elem.reportValidity() === false) {
      $(this).addClass('invalid');
      console.log("Invalid:" + $(this).attr('name'));
      fv = false;
      return false;
    }
  });
  if(reqtype != 'new') {
    //check for at least one line item
    if(! $("#lineitems").has("table").length) {
      $(".lierror").show();
      console.log("Invalid line items");
      fv = false;
    }
  }
  return fv;
}

var dz = $('#dropzone');
dz.on('drag dragover dragstart dragend dragenter dragleave drop', function(event) {
  event.preventDefault();
  event.stopPropagation();
})
.on('dragover dragenter', function() {
  dz.addClass('dragover-active');
})
.on('dragleave dragend drop', function() {
  dz.removeClass('dragover-active');
})
.on('drop', function(e) {
  var flist = e.originalEvent.dataTransfer.files;
  if(flist.length > 1) {
    alert("Drag only one file at a time.");
  } else {
    uploadFile(flist[0]);
  }
});

$("#shipvia").on('click', function() {
  $(this).val('');
});

$("#paperclip").click(function() {
  $("#reqfile").trigger('click');
});

$("input[name='litype']").on('change', function() {
  setlisection();
});

function setlisection() {
  var type = $('input[name="litype"]:checked').val();
  if(type == "C") {
    $("#lifields").hide();
    $("#desclabel").html('Comment');
  } else {
    $("#lifields").show();
    $("#desclabel").html('Description');
  }
}


$('input[name=autoxfer]').on('change', function() {
  //it should be impossible to have empty value
  $("#confirmbtn").prop('disabled', false);
});

$("#reqfile").on('change', function() {
  var fname = $(this).val();
  if(fname !== '') {
    var filedata = $('#reqfile').prop("files")[0];
    uploadFile(filedata);
  }
});

function uploadFile(filedata) {
  var fdata = new FormData();
  fdata.append('reqfile', filedata);
  //this appears in $_POST, not $_FILES!
  fdata.append('reqid', reqid);
  $.ajax({
    url: "procfile.php",
    dataType: 'text',
    cache: false,
    contentType: false,
    processData: false,
    data: fdata,
    type: 'POST',
    success: function(result) {
      var jresult = JSON.parse(result);
      if('error' in jresult) {
        alert(jresult['error']);
      } else {
        getatts(reqid);
      }
    }
  });
}

$("#assignbtn").click(function() {
  $("#assigndlg").modal('show');
});

$("#changestatus").click(function() {
  $("#statusdlg").modal('show');
});

$("#assignconfirm").click(function() {
  $("#assigndlg").modal('hide');
  var assignee = $("#assignee").val();
  var url = "assign.php";
  var pdata = {'assignto':assignee,'reqid':reqid};
  $.ajax({
    url: url,
    type: "POST",
    data: pdata,
    dataType: "html",
    success: function(newurl) {
      location = newurl;
    },
    error: function(err) {
      alert(err);
    }
  });
});

$("#statusconfirm").click(function() {
  $("#statusdlg").modal('hide');
  var newstatus = $("#newstatus").val();
  var url = "status.php";
  var pdata = {'newstatus':newstatus,'reqid':reqid};
  $.ajax({
    url: url,
    type: "POST",
    data: pdata,
    dataType: "html",
    success: function(newurl) {
      location = newurl;
    },
    error: function(err) {
      alert(err);
    }
  });
});

$("#savebtn").click(function() {
  $(".val-submit").removeProp('required');
  if(!formvalid('new')) {
    return;
  }
  if(reqtype == 'rfq') {
    $("#action").val("savequote");
  } else {
    $("#action").val("save");
  }
  $("#reqform").submit();
});

$("#firstsave").click(function() {
  $(".val-submit").removeProp('required');
  if(!formvalid('new')) {
    return;
  }
  if(reqtype == 'rfq') {
    $("#action").val("quoteandedit");
  } else {
    $("#action").val("saveandedit");
  }
  $("#reqform").submit();
});

$("#submitbtn").click(function() {
  $(".val-submit").prop('required', true);
  if(!formvalid()) {
    return;
  }
  var url = "getapprovers.php";
  var obo = $("#onbehalf").val();
  var pdata = {'obo':obo, 'amount':amt};
  $("#approverlist").empty();
  $.ajax({
    url: url,
    type: "POST",
    data: pdata,
    success: function(result) {
      var jresult = JSON.parse(result);
      //this was not the intended logic
      //if($.inArray(currentuser, jresult) > -1) {
      if(Number(currentusermax) >= Number(amt)) {
        $("#selfappprompt").show();
        $("#dlgapprove").show();
      } else {
        $("#selfappprompt").hide();
        $("#dlgapprove").hide();
      }
      $.each(jresult, function(k, v) {
        $("#approverlist").append("<option value='" + this + "'>" + this + "</option>");
      });
      $("#selsubmit").modal('show');
    }
  });
});

$("#dlgsubmit").click(function() {
  $("#action").val("submit");
  $("#submitto").val($("#approverlist").val());
  $("#reqform").submit();
});

$("#approvebtn, #dlgapprove").on('click', function(event) {
  event.preventDefault();
  $("#selsubmit").modal('hide');
  $("#confirmhead").html("Approve Requisition");
  //var amt = $('#total').html();
  $("#confirmprompt").html("Approve requisition for $" + amt + "?");
  $("#confirmbtn").prop('disabled', false);
  $("#confirmbtn").prop("data-action", "approve");
  $("#xferdiv").hide();
  $("#lineitemmsg").hide();
  $("#confirmdlg").modal('show');
});

$("#closebtn").click(function() {
  $("#confirmbtn").prop("data-action", "close");
  $("#confirmhead").html("Close Requisition");
  $("#confirmprompt").html("Do you wish to close this requisition so that no further action is taken on it?");
  var xferrad = $('input[name=autoxfer]');
  if(xferrad[0].checked || xferrad[1].checked) {
    $("#confirmbtn").prop('disabled', false);
  } else {
    $("#confirmbtn").prop('disabled', true);
  }
  if(linesreviewed) {
    $("#xferdiv").show();
    $("#lineitemmsg").hide();
  } else {
    $("#xferdiv").hide();
    if(status == 'Assigned' | status == 'Ordered' | status == 'Closed') {
      $("#lineitemmsg").show();
    }
    //user may close req, but it will not transfer
    $("#confirmbtn").prop('disabled', false);
  }
  $("#confirmdlg").modal('show');
});

$("#logoff").click(function() {
  $("#confirmhead").html("Log Off");
  $("#confirmprompt").html("You are logged in as " + currentuser + ". Do you wish to log off?");
  $("#confirmbtn").prop('disabled', false);
  $("#confirmbtn").prop("data-action", "logoff");
  $("#xferdiv").hide();
  $("#confirmdlg").modal('show');
});

$("#confirmbtn").click(function(event) {
  event.preventDefault();
  $("#confirmdlg").modal('hide');
  var confirmaction = $("#confirmbtn").prop("data-action");
  switch(confirmaction) {
    case 'delline':
      var lid = $(this).prop("lineid");
      var url = "delline.php";
      var pdata = {'lineid':lid};
      $.ajax({
        url: url,
        type: "POST",
        data: pdata,
        success: function() {
          getlineitems(reqid);
        }
      });
      break;
    case 'approve':
      $("#action").val("approve");
      $("#reqform").submit();
      break;
    case 'logoff':
      location = "login.php";
      break;
    case 'close':
      $("#xfer").val($('input[name="autoxfer"]:checked').val());
      $("#action").val("close");
      $("#reqform").submit();
      break;
    default:
      console.log("Confirm button clicked without a valid action.");
  }
});

$("#quotebtn").click(function() {
  $("#action").val("quote");
  $("#reqform").submit();
});

$("#retquote").click(function() {
  $("#action").val("rtnquote");
  $("#reqform").submit();
});

function getlineitems(id) {
  var url = "getlines.php?reqid=" + id + "&mode=edit";
  $.ajax({
    url: url,
    success: function(result) {
      var jresult = JSON.parse(result);
      var amt = jresult['total'];
      linesreviewed = jresult['linesreviewed'];
      $("#total").html(amt);
      var reqlines = jresult['lines'];
      $("#lineitems").html(reqlines);
      $(".lierror").hide();
      if(linesreviewed == true) {
        $("#lineiteminfo").hide();
        $("#orderedby").prop('disabled', false);
        $("#orderdate").prop('disabled', false);
      } else {
        if(status == 'Assigned' | status == 'Ordered' | status == 'Closed') {
          $("#lineiteminfo").show();
        }
        $("#orderedby").prop('disabled', true);
        $("#orderdate").prop('disabled', true);
      }
    }
  });
}

function getatts(id) {
  var url = "getatt.php?reqid=" + id;
  $.ajax({
    url: url,
    success: function(result) {
      var jresult = JSON.parse(result);
      var reqfiles = jresult['files'];
      $("#attachments").html(reqfiles);
    }
  });
  $("#reqfile").removeProp('files');
}

if($("input[name='priority']:checked").val() != 'specify') {
  $("#specdate").hide();
}

$("button[name='newline']").click(function() {
  clearlineitem();
  $("#linext").hide();
  $("#lineitem").modal('show');
});

$("#delline").click(function() {
  $("#confirmdlg").modal('hide');
  var lid = $(this).prop("lineid");
  var url = "delline.php";
  var pdata = {'lineid':lid};
  $.ajax({
    url: url,
    type: "POST",
    data: pdata,
    success: function() {
      getlineitems(reqid);
    }
  });
});

//apply event handlers to static ancestor because dom is added or changed later
$("#lineitems").on('click', ".dumpline", function() {
  var lid = $(this).parent().attr('data-id');
  var ldesc = $(this).parent().parent().next().find("td").html();
  var dp = "Are you sure you want to delete this line?<br>" + ldesc;
  $('#confirmhead').html("Delete Line");
  $('#confirmprompt').html(dp);
  $("#confirmbtn").prop('disabled', false);
  $('#confirmbtn').prop("lineid", lid);
  $('#confirmbtn').prop("data-action", "delline");
  $("#xferdiv").hide();
  $('#confirmdlg').modal('show');
});

$("#lineitems").on('click', ".edline", function() {
  var lid = $(this).parent().attr('data-id');
  showline(lid);
});

function showline(lid, defaultdate) {
  var thisline = $("td[data-id='" + lid + "']").closest('tr.lineitem');
  var nextid = thisline.nextAll('tr.lineitem').find('td.actions').attr('data-id');
  var url = "getline.php?lineid=" + lid;
  $.ajax({
    url: url,
    success: function(linedata) {
      if(nextid === undefined) {
        $("#linext").hide();
      } else {
        $("#linext").show();
        $("#linext").attr('nextid', nextid);
      }
      clearlineitem();
      jdata = JSON.parse(linedata);
      var litype = jdata['litype'];
      $("input[name='litype'][value='" + litype + "']").prop('checked', true);
      setlisection();
      var liqty = jdata['qty'];
      $('#lineid').val(lid);
      $('#liqty').val(liqty);
      $('#liunit').val(jdata['unit']);
      var deldate = jdata['deldate'];
      if(deldate) {
        $('#deldate').val(deldate);
      } else {
        if(defaultdate) {
          $('#deldate').val(defaultdate.toString());
        }
      }
      var liprice = jdata['price'];
      $('#liprice').val(liprice);
      $("#liextprice").val((Math.round(liqty * liprice * 100, 2) / 100).toFixed(2));
      $('#livendpn').val(jdata['vpartno']);
      $('#lilicorpn').val(jdata['lpartno']);
      $('#lidesc').val(jdata['description']);
      $('#licomment').val(jdata['comment']);
      $('#lineitem').modal('show');
    }
  });
}

function clearlineitem() {
  $('#lineid').val('');
  $('#liqty').val('');
  $('#liunit').val('');
  $("input[name='litype']").prop('checked', false);
  $('#deldate').val('');
  $('#liprice').val('');
  $("#liextprice").val('');
  $('#livendpn').val('');
  $('#lilicorpn').val('');
  $('#lidesc').val('');
  $('#licomment').val('');
}

$("#onbehalf").change(function() {
  var obo = $("#onbehalf").val();
  var url = "api/getaccts.php?obo=" + obo;
  var opts = "<option value=''>-select-</option>";
  $.ajax({
    url: url,
    success: function(accts) {
      var acctarr = JSON.parse(accts);
      for(var i = 0; i < acctarr['accts'].length; i++) {
        var anum = acctarr['accts'][i]['acctnum'];
        var adesc = acctarr['accts'][i]['description'];
        opts += "<option value='" + anum + "'>" + anum + " " + adesc.substring(0, 30) + "</option>";
      }
      $("select[name='account']").empty().append(opts);
      $("#dept").val(acctarr['dept']);
    }});
  $("#dispglaccount").html("");
  $("#glaccount").val("");
});
$("select[name='account']").change(function() {
  var glacct = $(this).val() + "-" + $("#dept").val();
  $("#glaccount").val(glacct);
  $("#dispglaccount").html(glacct);
});

$("input[name='priority']").change(function() {
  if($(this).val() == 'specify') {
    $("#specdate").show();
  } else {
    $("#specdate").hide();
  }
});
$("#liqty, #liprice").change(function() {
  var liqty = $("#liqty").val();
  var liprice = $("#liprice").val();
  $("#liextprice").val((Math.round(liqty * liprice * 100, 2) / 100).toFixed(2));
});
$("#lisave").click(function() {
  var lidata = $("#lineitemform").serialize();
  //console.log(lidata);
  $('#lineitem').modal('hide');
  $.ajax({
    url: "proclineitem.php",
    data: lidata,
    type: "POST",
    success: function(newurl) {
      if(newurl.length > 4) {
        //if url is returned, it wants a page reload, since it's a new req
        location = newurl;
      } else {
        //just reload existing line items and update total
        getlineitems(reqid);
      }
    }
  });
});
$("#linext").click(function() {
  var nextline = $(this).attr('nextid');
  var lid = $('#lineid').val();
  var lidata = $("#lineitemform").serialize();
  var deldate = $("#deldate").val();
  $.ajax({
    url: "proclineitem.php",
    data: lidata,
    type: "POST",
    success: function(newurl) {
      if(newurl.length > 4) {
        //if url is returned, it wants a page reload, since it's a new req
        location = newurl;
      } else {
        //just reload existing line items and update total
      var nextid = $(this).closest('tr.lineitem').nextAll('tr.lineitem').find('td.actions').attr('data-id');
      getlineitems(reqid);
      //transfer deldate to next line item if it does not already have one
      showline(nextline, deldate);
      }
    }
  });
});
$('.md-36').popover({placement: 'bottom', trigger: 'hover'});
$("#sugvnum").autocomplete({
  source: "api/getvend-w-phone.php",
  minLength: 3,
  delay: 360,
  position: {my: "center top", at: "center bottom"},
  select: function(event, ui) {
    $("#sugvname").val(ui.item.vendorname);
    $("#dispsugvname").html(ui.item.vendorname);
    $("#sugvphone").val(ui.item.phone);
  },
  change: function(event, ui) {
    if(!ui.item) {
      $("#sugvname").val($("#sugvnum").val());
      $("#sugvphone").val('');
      $("#dispsugvname").html($("#sugvnum").val());
      console.log('nothing');
    }
  }
});
$("#actvnum").autocomplete({
  source: "api/getvend-w-phone.php",
  minLength: 3,
  delay: 360,
  position: {my: "center top", at: "center bottom"},
  select: function(event, ui) {
    $("#actvname").val(ui.item.vendorname);
    $("#dispactvname").html(ui.item.vendorname);
    $("#actvphone").val(ui.item.phone);
  },
  change: function(event, ui) {
    if(!ui.item) {
      $("#actvname").val($("#actvnum").val());
      $("#actvphone").val('');
      $("#dispactvname").html($("#actvnum").val());
    }
  }
});
