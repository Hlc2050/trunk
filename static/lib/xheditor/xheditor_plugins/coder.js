var coderPlugin = {
highlight: {
c: 'coderPlugin',
t: '插入代码',
s: null,
h: 1,
e: function () {
var _this = this;
var selectHtml = "<select id='xheCodeLanguages'>";
selectHtml += "<option value='code'>代码</option>";
selectHtml += "<option value='javascript'>JS</option>";
selectHtml += "<option value='css'>CSS</option>";
selectHtml += "<option value='php'>php</option>";
selectHtml += "<option value='html'>HTML</option>";
selectHtml += "<option value='xml'>XML</option>";
selectHtml += "</select>";
var jTest = $('<div><b>请输入代码</b>' + selectHtml + '</div><div><textarea id="xheTestInput" style="width:280px;height:120px;"></textarea></div><div style="text-align:right;"><input type="button" id="xheSave" value="确 定" /></div>');
var jTestInput = $('#xheTestInput', jTest), jSave = $('#xheSave', jTest);
jSave.click(function () {
var inputCode = jTestInput.val();
inputCode = "<div style='border:solid #ccc 1px;width:98%;overflow:auto;'>"
+ "<div style='background-color:#ddd; border-bottom:solid 1px #ccc; line-height:1.5em; text-indent:0.5em;'>" + $("#xheCodeLanguages").val() + "代码</div>"
+ "<pre class='brush: " + $("#xheCodeLanguages").val() + "' >"
+ inputCode.replace(/</g, "&lt;").replace(/>/g, "&gt;")
+ "</pre>"
+ "</div>";
_this.loadBookmark();
_this.pasteHTML(inputCode);
_this.hidePanel();
return false;
});
_this.showDialog(jTest);
}
}
}; 