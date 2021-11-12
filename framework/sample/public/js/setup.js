EphectSetup = {};
EphectSetup.rewriteBase = "/";
EphectSetup.URL = window.location.href;
EphectSetup.isReady = false;
EphectSetup.stepsResult = [];
EphectSetup.stepsCount = document.querySelectorAll("div[data-step]").length;

EphectSetup.checkSteps = function (stepResult, callback) {
  EphectSetup.stepsResult.push(stepResult);
  if (EphectSetup.stepsResult.length !== EphectSetup.stepsCount) {
    return;
  }

  var finalResult = true;
  for (var key in EphectSetup.stepsResult) {
    var currentResult = EphectSetup.stepsResult[key];
    finalResult = finalResult && currentResult;
  }

  EphectSetup.isReady = finalResult;
  if (finalResult && "function" == typeof callback) {
    callback.call(this);
  }
};

EphectSetup.callback = function () {
  if (EphectSetup.isReady) {
    setTimeout(function () {
      window.location = EphectSetup.rewriteBase;
    }, 1000);
  }
};

EphectSetup.process = function (callback) {
  EphectSetup.ajax(
    EphectSetup.URL,
    {
      action: "rewrite",
    },
    function (data) {
      if (!data.error) {
        EphectSetup.rewriteBase = data.result;
      }
      var result = EphectSetup.operationState(1, data.error);
      EphectSetup.checkSteps(result, EphectSetup.callback);
    }
  );

  EphectSetup.ajax(
    EphectSetup.URL,
    {
      action: "js",
    },
    function (data) {
      var result = EphectSetup.operationState(2, data.error);
      EphectSetup.checkSteps(result, EphectSetup.callback);
    }
  );

  EphectSetup.ajax(
    EphectSetup.URL,
    {
      action: "index",
    },
    function (data) {
      var result = EphectSetup.operationState(3, data.error);
      EphectSetup.checkSteps(result, EphectSetup.callback);
    }
  );
};

EphectSetup.ajax = function (url, data, callback) {
  var params = [];

  for (var key in data) {
    if (data.hasOwnProperty(key)) {
      params.push(key + "=" + encodeURI(data[key]));
    }
  }

  var queryString = params.join("&");
  var xhr = new XMLHttpRequest();
  xhr.open("POST", url);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.setRequestHeader(
    "Accept",
    "application/json, text/javascript, */*; q=0.01"
  );
  xhr.onload = function () {
    if (xhr.status < 300 || xhr.status === 304) {
      console.log({ res: xhr.responseText });
      var data = xhr.responseText !== "" ? JSON.parse(xhr.responseText) : [];

      if (typeof callback == "function") {
        callback.call(this, data, xhr.statusText, xhr);
      }
    }
  };
  xhr.onerror = function () {
    xhr.abort();
  };
  xhr.onabort = function () {
    if (xhr.statusText === "error") {
      errorLog(
        "Satus : " +
          xhr.status +
          "\r\n" +
          "Options : " +
          xhr.statusText +
          "\r\n" +
          "Message : " +
          xhr.responseText
      );
    }
  };

  xhr.send(queryString);
};

EphectSetup.operationState = function (step, error) {
  var result = false;

  if (error) {
    document.querySelector('div[data-step="' + step + '"]').innerHTML = "Error";
    document.querySelector('div[data-step="' + step + '"]').className =
      "step error";
  }

  if (!error) {
    document.querySelector('div[data-step="' + step + '"]').innerHTML = "OK!";
    document.querySelector('div[data-step="' + step + '"]').className =
      "step ok";
    result = true;
  }

  return result;
};

EphectSetup.process();
