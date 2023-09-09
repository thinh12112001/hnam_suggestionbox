
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
    console.log(cookie);
    document.cookie = cookie;
  }

  function getCookieValue(cookieName) {
    var name = cookieName + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    
    // Sử dụng biểu thức chính quy để tách các cookie
    var cookieArray = decodedCookie.split(/;\s*/);

    // var cookieArray = decodedCookie.split('  ');
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

function updateCookie(cookieName) {
    var decodedCookie = decodeURIComponent(document.cookie);
    var cookieArray = decodedCookie.split(/;\s*/);
    
    for (var i = 0; i < cookieArray.length; i++) {
      var cookie = cookieArray[i];
      var cookieParts = cookie.split('=');
      var currentCookieName = cookieParts[0].trim();
      
      if (currentCookieName === cookieName) {
        var expirationDate = new Date();
        expirationDate.setMonth(expirationDate.getMonth() + 1);
        cookie += '; expires=' + expirationDate.toUTCString();
        document.cookie = cookie;
        break;
      }
    }
  }
  

function suggestion(){
    var cookieExists = checkCookieExists('user-trip');
    var cookieValue;
    var url = window.location.href;
    var searchString = "xyz";

    var currentURL ="";
    if (url.includes(searchString)) {
        currentURL = window.location.href;
    }

    if (cookieExists) {
        var cookieUserTrip = getCookieValue('user-trip');
        console.log(cookieUserTrip);
        var cookieObject = JSON.parse(cookieUserTrip);
        

        var uid = cookieObject.uid;
        var currentBrowser = cookieObject.currentBrowser;

        updateCookie('user-trip');
        
      } else {
        
        // format currentBrowser
        var currentBrowser = navigator.userAgent;
        if (currentBrowser.includes(';')) {
            currentBrowser = currentBrowser.replace(/;/g, ' ');
        }

        // var currentBrowser = "";
        // if (userAgent.match(/(edge|edgios|edga|edg)/i)) {
        //     currentBrowser = "Microsoft Edge";
        // } else if (userAgent.match(/(opr|opera)/i)) {
        //     currentBrowser = "Opera";
        // } else if (userAgent.match(/(chrome|crios)/i)) {
        //     currentBrowser = "Google Chrome";
        // } else if (userAgent.match(/firefox|fxios/i)) {
        //     currentBrowser = "Mozilla Firefox";
        // } else if (userAgent.match(/safari/i)) {
        //     currentBrowser = "Safari";
        // } else {
        //     currentBrowser = "Unknown";
        // }
        
        // create random UID
        // var currentTime = new Date().getTime();
        // var randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        // var uid = currentTime + randomNum;

        var currentTime = new Date();
        var day = currentTime.getDate().toString().padStart(2, '0');
        var month = (currentTime.getMonth() + 1).toString().padStart(2, '0'); // Tháng trong JavaScript được đếm từ 0 đến 11
        var year = currentTime.getFullYear().toString().slice(-1);
        var date = day + month + year;
        var randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        var uid = date + randomNum;
        console.log(uid);

        cookieValue  = JSON.stringify( {
            uid : uid,
            currentURL: currentURL,
            currentBrowser: currentBrowser,
        });
        createCookie('user-trip', cookieValue, 700, '/');
      }
        if (currentURL !== "") {
            var formData = new FormData();
            formData.append('uid', uid); 
            formData.append('currentBrowser', currentBrowser);
            formData.append('url_tracking', currentURL);
   
            ajax({
                url: 'http://internal_hnammobile.com/suggestion',
                type: 'POST',
                data: formData,
                before: function() {
                },
                success: function(response) {
                    
                    // Chuỗi chứa cả nội dung HTML và CSS
                    var combinedString = response;
                    // Tìm vị trí của thẻ <style>
                    var styleStartIndex = combinedString.indexOf("<style>");
                    var styleEndIndex = combinedString.indexOf("</style>") + "</style>".length;

                    // Tách chuỗi CSS từ vị trí bắt đầu đến vị trí kết thúc của thẻ <style>
                    var cssString = combinedString.substring(styleStartIndex, styleEndIndex);

                    // Tách chuỗi HTML bằng cách loại bỏ chuỗi CSS
                    var htmlString = combinedString.replace(cssString, "");

                    // Hiển thị chuỗi CSS và HTML
                    // console.log("CSS: " + cssString);
                    // console.log("HTML: " + htmlString);

                    var divProductItems = document.querySelector('#hnam_internal');
                    divProductItems.innerHTML = htmlString;
                    document.head.innerHTML +=cssString;
                    
                },
                error: function() {
                    alert('Lỗi hệ thống! Vui lòng thử lại sau.');
                },
                processForm: true,
            });
        }
}
try {
    suggestion();
} catch (error) {
    
}

