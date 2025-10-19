/**
 * variables
 */
const isMobile = navigator.userAgent.match(/Mobile/i) == "Mobile";
const ajaxUrl = "/wp-admin/admin-ajax.php";
const siteCookieDomain = "."+document.location.hostname.replace("www.","");
const cookieParamsAdd = {
    expires: 356,
    path: "/",
    secure: false
};
const cookieParamsRemove = {
    expires: -1,
    path: "/",
    secure: false
};