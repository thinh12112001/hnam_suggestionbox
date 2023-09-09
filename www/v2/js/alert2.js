
function isFunction(possibleFunction) {
    return typeof(possibleFunction) === typeof(Function);
}
function params(object) {
    var encodedString = '';
    for (var prop in object) {
        if (object.hasOwnProperty(prop)) {
            if (encodedString.length > 0) {
                encodedString += '&';
            }
            encodedString += encodeURI(prop + '=' + object[prop]);
        }
    }
    return encodedString;
}

function ajax(obj) {
    if (isFunction(obj.before)) {
        obj.before();
    }
    var xhr = new XMLHttpRequest();
    var type = obj.type.toLowerCase();
    var data = null;
    if (obj.data) {
        data = params(obj.data);
    }
    if (type=='get') {
        xhr.open('GET', obj.url);
    }
    if (type=='post') {
        xhr.open('POST', obj.url);
        if (obj.processForm == true) {
            var data = obj.data;
        }
        else {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
    }
    xhr.onload = function() {
        if (xhr.status === 200) {
            obj.success(xhr.response);
        }
        else if (xhr.status !== 200) {
            if (undefined !== obj.error) {
                obj.error();
            }
        }
    };
    if (obj.data) {
        // alert(obj.data);
        xhr.send(data);
    }
    else {
        xhr.send();
    }
}

function checkCookieExists(cookieName) {
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
      var cookie = cookies[i].trim();
      if (cookie.indexOf(cookieName + '=') === 0) {
        return true;
      }
    }
    return false; 
  }
  
function createCookie(cookieName, cookieValue, expires, path) {
    var cookie = encodeURIComponent(cookieName) + '=' + encodeURIComponent(cookieValue);
    
    if (expires) {
      var expirationDate = new Date();
      expirationDate.setTime(expirationDate.getTime() + expires * 24 * 60 * 60 * 1000);
      cookie += '; expires=' + expirationDate.toUTCString();
    }
    
    if (path) {
      cookie += '; path=' + path;
    }
    
    document.cookie = cookie;
  }

function getCookieValue(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    
    // Sử dụng biểu thức chính quy để tách các cookie
    var cookieArray = decodedCookie.split(/;\s*/);
    
    for (var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i];
        while (cookie.charAt(0) === ';') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) === 0) {
            var value = cookie.substring(name.length);
            // Kiểm tra nếu sau dấu ";" không còn ký tự nào khác
            if (value.charAt(value.length - 1) === ';' && value.length === 1) {
                return '';
            } else {
                return value;
            }
        }
    }
    return null;
}

function updateCookie(cookieName, cookieValue, expires, path) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(/;\s*/);
    
    for (var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i];
        while (cookie.charAt(0) === ';') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(name) === 0) {
            var updatedCookie = name + cookieValue;
            if (expires) {
                updatedCookie += "; expires=" + expires.toUTCString();
            }
            if (path) {
                updatedCookie += "; path=" + path;
            }
            document.cookie = updatedCookie;
            return;
        }
    }
    
    // Nếu không tìm thấy cookie, tạo mới cookie với giá trị chỉ định
    createCookie(cookieName, cookieValue, expires, path);
}


function trackingTrip(){
    var cookieExists = checkCookieExists('user-trip');
    var cookieValue;
    var url = window.location.href;
    var searchString = "https://www.hnammobile.com/dien-thoai";

    var currentURL ="";
    if (url.includes(searchString)) {
        currentURL = window.location.href;
    }

    if (cookieExists) {
        var cookieUserTrip = getCookieValue('user-trip');
        console.log(cookieUserTrip);
        var cookieObject;
        if (cookieUserTrip.indexOf('=') !== -1) {
            cookieObject = JSON.parse(cookieUserTrip.substring(cookieUserTrip.indexOf('=') + 1));
        } else {
            cookieObject = JSON.parse(cookieUserTrip);
        }
        
        //format currentTime
        var currentTime = new Date();
        var year = currentTime.getFullYear();
        var month = String(currentTime.getMonth() + 1).padStart(2, '0');
        var day = String(currentTime.getDate()).padStart(2, '0');
        var hours = String(currentTime.getHours()).padStart(2, '0');
        var minutes = String(currentTime.getMinutes()).padStart(2, '0');
        var seconds = String(currentTime.getSeconds()).padStart(2, '0');
        var timeStamp = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

        var uid = cookieObject.uid;
        

        var currentBrowser = cookieObject.currentBrowser;

        cookieValue  = JSON.stringify( {
            uid : uid,
            timeStamp: timeStamp,
            currentURL: currentURL,
            currentBrowser: currentBrowser
        });

        updateCookie('user-trip', cookieValue, new Date('2023-12-31'), '/');
        
      } else {
        
        // format currentBrowser
        var userAgent = navigator.userAgent;
        var currentBrowser = "";
        if (userAgent.match(/(edge|edgios|edga|edg)/i)) {
            currentBrowser = "Microsoft Edge";
        } else if (userAgent.match(/(opr|opera)/i)) {
            currentBrowser = "Opera";
        } else if (userAgent.match(/(chrome|crios)/i)) {
            currentBrowser = "Google Chrome";
        } else if (userAgent.match(/firefox|fxios/i)) {
            currentBrowser = "Mozilla Firefox";
        } else if (userAgent.match(/safari/i)) {
            currentBrowser = "Safari";
        } else {
            currentBrowser = "Unknown";
        }
        // format currentTime
        var currentTime = new Date();
        var year = currentTime.getFullYear();
        var month = String(currentTime.getMonth() + 1).padStart(2, '0');
        var day = String(currentTime.getDate()).padStart(2, '0');
        var hours = String(currentTime.getHours()).padStart(2, '0');
        var minutes = String(currentTime.getMinutes()).padStart(2, '0');
        var seconds = String(currentTime.getSeconds()).padStart(2, '0');
        var timeStamp = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
        // create random UID
        var currentTime = new Date().getTime();
        var randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        var uid = currentTime + randomNum;

        cookieValue  = JSON.stringify( {
            uid : uid,
            currentURL: currentURL,
            currentBrowser: currentBrowser,
            timeStamp: timeStamp
        });
        createCookie('user-trip', cookieValue, 700, '/');
      }
        if (currentURL !== "") {
            var formData = new FormData();
            formData.append('cookieValue', cookieValue); 
            ajax({
                url: 'http://internal_hnammobile.com/tracking',
                type: 'POST',
                data: formData,
                before: function() {
                },
                success: function(response) {
                },
                error: function() {
                    alert('Lỗi hệ thống! Vui lòng thử lại sau.');
                },
                processForm: true,
            });
        }
}


function getIP() {
    return new Promise((resolve, reject) => {
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => resolve(data.ip))
            .catch(error => reject(error));
    });
}
trackingTrip();
