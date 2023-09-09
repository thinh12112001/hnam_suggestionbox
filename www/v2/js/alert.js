// alert file
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
function ajaxForJson(obj) {
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
            xhr.setRequestHeader('Content-Type', 'application/json'); // Đặt tiêu đề Content-Type là application/json
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

function fetchAjax(obj) {
    if (isFunction(obj.before)) {
        obj.before();
    }

    const options = {
        method: obj.type,
        headers: {},
    };

    if (obj.data) {
        if (obj.type === 'post' && !obj.processForm) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(obj.data);
        } else {
            options.body = new URLSearchParams(obj.data).toString();
        }
    }

    fetch(obj.url, options)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(obj.success)
        .catch((error) => {
            console.error('Error occurred:', error); // Log the error details to the console
            if (obj.error) {
                obj.error(error);
            }
        });
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
    // console.log(cookie);
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

function updateCookie(cookieName, fullContent) {
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
  

function trackingTrip(){
    var cookieExists = checkCookieExists('user-trip');
    var cookieValue;
    var url = window.location.href;
    var searchString = "https://www.hnammobile.com";
    var currentURL = window.location.href;
    
    if (cookieExists) {
        var cookieUserTrip = getCookieValue('user-trip');
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

        var currentTime = new Date();
        var day = currentTime.getDate().toString().padStart(2, '0');
        var month = (currentTime.getMonth() + 1).toString().padStart(2, '0'); // Tháng trong JavaScript được đếm từ 0 đến 11
        var year = currentTime.getFullYear().toString().slice(-1);
        var date = day + month + year;
        var randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        var uid = date + randomNum;
        // console.log(uid);


        cookieValue  = JSON.stringify( {
            uid : uid,
            currentURL: currentURL,
            currentBrowser: currentBrowser,
            // fullContent: extractedContent
        });
        createCookie('user-trip', cookieValue, 700, '/');
      }
        var fullContent = document.body.outerHTML;

        if (currentURL !== "") {
            var formData = new FormData();
            formData.append('uid', uid); 
            formData.append('currentBrowser', currentBrowser);
            formData.append('url_tracking', currentURL);
            formData.append('fullContent',fullContent);
            ({
                url: 'https://int.hnammobile.com/tracking',
                type: 'POST',
                data: formData,
                before: function() {
                },
                success: function(response) {
                   
                },
                error: function() {
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

function suggestion(){
    var cookieExists = checkCookieExists('user-trip');
    var currentURL = window.location.href;


    if (cookieExists) {
        var cookieUserTrip = getCookieValue('user-trip');
        // console.log(cookieUserTrip);
        var cookieObject = JSON.parse(cookieUserTrip);

        var uid = cookieObject.uid;

        updateCookie('user-trip');
        
      } else {
        var currentTime = new Date();
        var day = currentTime.getDate().toString().padStart(2, '0');
        var month = (currentTime.getMonth() + 1).toString().padStart(2, '0'); // Tháng trong JavaScript được đếm từ 0 đến 11
        var year = currentTime.getFullYear().toString().slice(-1);
        var date = day + month + year;
        var randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        var uid = date + randomNum;
        
        cookieValue  = JSON.stringify( {
            uid : uid,
            currentURL: currentURL,
        });
        createCookie('user-trip', cookieValue, 700, '/');
      }
        if (currentURL !== "") {
            var formData = new FormData();
            formData.append('uid', uid); 
            formData.append('url_tracking', currentURL);
   
            ajax({
                url: 'https://int.hnammobile.com/suggestion',
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },
                type: 'POST',
                data: formData,
                before: function() {
                },
                success: function(response) {
                    if (!response == "") {
                        var combinedString = response;
                        var styleStartIndex = combinedString.indexOf("<style>");
                        var styleEndIndex = combinedString.indexOf("</style>") + "</style>".length;

                        var cssString = combinedString.substring(styleStartIndex, styleEndIndex);
                        var htmlString = combinedString.replace(cssString, "");

                        if (document.querySelector('#hnam_internal')){
                            var divProductItems = document.querySelector('#hnam_internal');
                            divProductItems.innerHTML = htmlString;
                            document.head.innerHTML +=cssString;
                        } 
                    }
                                      
                },
                error: function() {
                },
                processForm: true,
            });
        }
}

function suggestForBlog() {
    var currentURL = window.location.href;
    var baseUrl = "https://www.hnammobile.com/tin-tuc";
    if (currentURL.includes(baseUrl)) {
        var uid = 0;
        var cookieExists = checkCookieExists('user-trip');
        if (cookieExists) {
            var cookieUserTrip = getCookieValue('user-trip');
            
            var cookieObject = JSON.parse(cookieUserTrip);
            uid = cookieObject.uid;
        }
        

        var formData = new FormData();
        formData.append('current_url', currentURL);
        formData.append('uid', uid);
        // gửi cookie 
        

        ajax({
            url: 'https://int.hnammobile.com/suggestionforblog',
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            type: 'POST',
            data: formData,
            before: function() {
            },
            success: function(response) {
                if (!response == "") {
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
                    if (document.querySelector('#hnam_internal_blog')){
                        var divProductItems = document.querySelector('#hnam_internal_blog');
                        divProductItems.innerHTML = htmlString;
                        document.head.innerHTML +=cssString;
                    } 
                }
                                  
            },
            
            error: function() {
            },
            processForm: true,
        });
    }
}

function getParameterValue(queryString, parameterName) {
    const params = new URLSearchParams(queryString);
    return params.get(parameterName);
  }

function trackingblog() {

    var currentURL = window.location.href;
    var baseUrl = "https://www.hnammobile.com/";
    var keyword1= "utm_source";
    var keyword2 = "hnam-suggestion";
    
    if (currentURL.includes(keyword1) && currentURL.includes(keyword2) && currentURL.includes(baseUrl)) {
        
        let queryString = currentURL.split('?')[1];
        let queryStringDecode = decodeURIComponent(queryString);
        
        const currentUrlValue = getParameterValue(queryStringDecode, "currentUrl");
        const itemIdValue = getParameterValue(queryStringDecode, "itemid");
        
        var uid = 0;
        var cookieExists = checkCookieExists('user-trip');    
        if (cookieExists) {
            var cookieUserTrip = getCookieValue('user-trip');
            var cookieObject = JSON.parse(cookieUserTrip);
            uid = cookieObject.uid;
        }
            var formData = new FormData();
            formData.append('current_url', currentUrlValue);
            formData.append('uid', uid);
            formData.append('itemIdValue', itemIdValue);
            
            
            ajax({
                url: 'https://int.hnammobile.com/suggestionforblog/trackingblog',
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },
                type: 'POST',
                data: formData,
                before: function() {
                },
                success: function(response) { 
                },
                error: function() {
                },
                processForm: true,
            });
        
    }
}


function trackingTransaction() {

    var currentURL = window.location.href;
    var baseUrl = "https://www.hnammobile.com/gio-hang";
    
    if (currentURL.includes(baseUrl)) {
        var uid = 0;

  
        var cookieExists = checkCookieExists('user-trip');    

        if (cookieExists) {
            var cookieUserTrip = getCookieValue('user-trip');

            // console.log(cookieUserTrip);
            var cookieObject = JSON.parse(cookieUserTrip);

            uid = cookieObject.uid;
        }
            var formData = new FormData();
            formData.append('current_url', currentURL);
            formData.append('uid', uid);
            
            // var jsonData = JSON.stringify({ uid: uid, current_url: currentURL });
            ajax({
                url: 'https://int.hnammobile.com/suggestionforblog/trackingtransaction',
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },
                type: 'POST',
                data: formData,
                before: function() {
                },
                success: function(response) { 
                },
                error: function() {
                },
                processForm: true,
            });
        
    }
}

function trackingcart() {
    var currentURL = window.location.href;
    var baseUrl = "https://www.hnammobile.com/shopping-bag/quick-preview-cart?order_id";
    // var startTime = new Date();
    if (currentURL.includes(baseUrl)) {
        // document.addEventListener('DOMContentLoaded', function() {
            let quantityElements = document.querySelectorAll(".product-item-value-quantity");
            const idElements = document.querySelectorAll(".product-item-value-id");
            if (quantityElements.length > 0 && idElements.length > 0) {
                let idQuantityArray = [];
                for (let i = 0; i < quantityElements.length; i++) {
                    const id = idElements[i].value;
                    const quantity = quantityElements[i].value;
                    idQuantityArray.push({ id, quantity });
                }

                console.log("ID: Quantity Array:", idQuantityArray);

                var uid = 0;
                var cookieExists = checkCookieExists('user-trip');    
                if (cookieExists) {
                    var cookieUserTrip = getCookieValue('user-trip');
                    var cookieObject = JSON.parse(cookieUserTrip);
                    uid = cookieObject.uid;
                }
                    var formData = new FormData();
                    formData.append('uid', uid);
                    formData.append('currentURL', currentURL);
                    formData.append('cartValue', JSON.stringify(idQuantityArray));

                    ajax({
                        url: 'https://int.hnammobile.com/suggestionforblog/trackingcart',
                        crossDomain: true,
                        xhrFields: {
                            withCredentials: true
                        },
                        type: 'POST',
                        data: formData,
                        before: function() {
                        },
                        success: function(response) { 
                        },
                        error: function() {
                        },
                        processForm: true,
                    });

                    // var loadTime = new Date();
                    // var timeElapsed = loadTime - startTime;
                    // console.log('Thời gian tải xong mọi tài nguyên (load): ' + timeElapsed + 'ms');
            } else {
                    console.log("quantityElements and/or idElements do not exist or are empty.");
                }
        // });  
    }
}


trackingTrip();


try {
    suggestion();
    suggestForBlog();
    trackingTransaction();   
    trackingblog();
    trackingcart();

} catch (error) {
    console.log(error);
}
