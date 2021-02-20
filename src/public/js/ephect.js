/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Ephect {
  static DOM () {}
  /**
     * Performs a AJAX request and return the result to a callback function
     *
     * @param {type} url
     * @param {type} callback
     * @returns JSON stream on callback
     */
  static ajax (url, data, callback) {
    Ephect.Rest.post(url, data, callback)
  }
}

Ephect.DOM.ready = function (f) {
  /in/.test(document.readyState)
    ? setTimeout('Ephect.DOM.ready(' + f + ')', 9)
    : f()
}

Ephect.Rest = (function () {
  class F {
    /**
       * Performs a HEAD request and return the result to a callback function
       *
       * @param {type} url
       * @param {type} callback
       * @returns JSON stream
       */
    head (url, callback) {
      var xhr = new XMLHttpRequest()
      xhr.setRequestHeader('Accept', 'application/json')
      xhr.open('HEAD', url)
      xhr.onload = function () {
        if (typeof callback === 'function') {
          if (xhr.status === 200) {
            var data =
                xhr.responseText !== '' ? JSON.parse(xhr.responseText) : []
            callback.call(this, data)
          } else {
            callback.call(this, xhr.status)
          }
        }
      }
      xhr.send()
    }

    /**
       * Performs a GET request and return the result to a callback function
       *
       * @param {type} url
       * @param {type} callback
       * @returns JSON stream
       */
    get (url, callback) {
      var xhr = new XMLHttpRequest()
      xhr.setRequestHeader('Accept', 'application/json')
      xhr.open('GET', url)
      xhr.onload = function () {
        if (typeof callback === 'function') {
          if (xhr.status === 200) {
            var data =
                xhr.responseText !== '' ? JSON.parse(xhr.responseText) : []
            callback.call(this, data)
          } else {
            callback.call(this, xhr.status)
          }
        }
      }
      xhr.send()
    }

    /**
       * Performs a POST request and return the result to a callback function
       *
       * @param {type} url
       * @param {type} callback
       * @returns JSON stream on callback
       */
    post (url, data, callback) {
      var xhr = new XMLHttpRequest()
      var params = ''
      for (var key in data) {
        if (undefined !== data[key]) {
          params += '&' + encodeURI(key + '=' + data[key])
        }
      }
      params = params.substring(1)
      xhr.setRequestHeader('Accept', 'application/json')
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
      xhr.open('POST', url)
      xhr.onload = function () {
        if (typeof callback === 'function') {
          if (xhr.status === 200) {
            var data =
                xhr.responseText !== '' ? JSON.parse(xhr.responseText) : []
            callback.call(this, data)
          } else {
            callback.call(this, xhr.status)
          }
        }
      }
      xhr.send(params)
    }

    /**
       * Performs a PATCH request and return the result to a callback function
       *
       * @param {type} url
       * @param {type} callback
       * @returns JSON stream on callback
       */
    patch (url, data, callback) {
      var xhr = new XMLHttpRequest()
      var params = ''
      for (var key in data) {
        if (undefined !== data[key]) {
          params += '&' + encodeURI(key + '=' + data[key])
        }
      }
      params = params.substring(1)
      xhr.setRequestHeader('Accept', 'application/json')
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
      xhr.open('PATCH', url)
      xhr.onload = function () {
        if (typeof callback === 'function') {
          if (xhr.status === 200) {
            var data =
                xhr.responseText !== '' ? JSON.parse(xhr.responseText) : []
            callback.call(this, data)
          } else {
            callback.call(this, xhr.status)
          }
        }
      }
      xhr.send(params)
    }

    /**
       * Performs a PUT request and return the result to a callback function
       *
       * @param {type} url
       * @param {type} callback
       * @returns JSON stream on callback
       */
    put (url, data, callback) {
      var xhr = new XMLHttpRequest()
      xhr.setRequestHeader('Accept', 'application/json')
      xhr.setRequestHeader('Content-Type', 'application/json')
      xhr.open('PUT', url)
      xhr.onload = function () {
        if (typeof callback === 'function') {
          if (xhr.status === 200) {
            var data =
                xhr.responseText !== '' ? JSON.parse(xhr.responseText) : []
            callback.call(this, data)
          } else {
            callback.call(this, xhr.status)
          }
        }
      }
      xhr.send(JSON.stringify(data))
    }

    /**
       * Performs a DELETE request and return the result to a callback function
       *
       * @param {type} url
       * @param {type} callback
       * @returns JSON stream on callback
       */
    delete (url, callback) {
      var xhr = new XMLHttpRequest()
      xhr.setRequestHeader('Accept', 'application/json')
      xhr.open('DELETE', url)
      xhr.onload = function () {
        if (typeof callback === 'function') {
          if (xhr.status === 200) {
            var data =
                xhr.responseText !== '' ? JSON.parse(xhr.responseText) : []
            callback.call(this, data)
          } else {
            callback.call(this, xhr.status)
          }
        }
      }
      xhr.send()
    }
  }

  return new F()
})()

