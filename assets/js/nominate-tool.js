(function () {
	console.log('Nominate This Setup');

	if (null !== document.getElementById('pressforward-nt')) {
		window.pfnt_deactivate();
	}
	//Coming in:
	//var d=document,
	//w=window,
	//e=w.getSelection,
	//k=d.getSelection,
	//x=d.selection,
	//s=(e?e():(k)?k():(x?x.createRange().text:0)),
	//l=d.location,
	//e=encodeURIComponent,
	//

	//https://docs.ckeditor.com/ckeditor5/latest/builds/guides/quick-start.html#classic-editor

	window.pfnt = {};
	window.pfnt.windows = {};
	window.pfnt.windows.links = [];
	window.pfnt.windows.scripts = [];
	window.pfnt.windows.styleBlocks = [];
	window.pfnt.styles = {};
	window.pfnt.tools = {};
	window.pfReadability = {};
	window.pfnt.key = window.ku;
	window.pfnt.selection = window.getSelection().toString();
	window.pfnt.submitObject = {
		post_title: false,
		item_link: window.location.href,
		pf_source_link: window.location.origin,
		// post_type: false,
		post_status: false,
		content: false, //post_content
		tax_input: false,
		item_feat_img: false,
		item_author: false,
		post_tags: false,
		user_key: window.ku,
		photo_src: [],
		photo_description: [],
		action: 'post',
	};

	function generateTag(el, id, className, style) {
		var aTag = document.createElement(el);
		aTag.setAttribute('id', id);
		aTag.setAttribute('class', className);
		aTag.setAttribute('style', style);
		return aTag;
	}

	function textareaBuilder(contents) {
		var editorContainer = generateTag('div', 'wp-nomthis-editor-container', 'wp-editor-container', '');

		var toolbar = generateTag('div', 'qt_nomthis_toolbar', 'quicktags-toolbar', '');

		var nomTextArea = generateTag('textarea', 'nominateText', 'wp-editor-area', 'height:375px;');
		nomTextArea.setAttribute('name', 'post_content');
		nomTextArea.setAttribute('autocomplete', 'off');
		nomTextArea.setAttribute('cols', '40');
		nomTextArea.innerHTML = contents;
		// editorContainer.appendChild(toolbar);
		editorContainer.appendChild(nomTextArea);
		window.pfnt.windows.editorContainer = editorContainer;
		return editorContainer;
	}

	function linkElement(linkUrl, rel, type) {
		//var linkUrl = 'https://cdn.quilljs.com/1.0.0/quill.snow.css';
		var linkE = document.createElement('link');
		linkE.setAttribute('rel', rel);
		linkE.setAttribute('href', linkUrl);
		linkE.setAttribute('type', type);
		// linkE.setAttribute('media', 'all');
		var headTag = document.getElementsByTagName('head')[0];
		window.pfnt.windows.links.push(linkE);
		headTag.prepend(linkE);
		return linkE;
	}

	function scriptElement(scriptUrl) {
		//var linkUrl = 'https://cdn.quilljs.com/1.0.0/quill.snow.css';
		var scriptE = document.createElement('script');
		//scriptE.setAttribute('src', scriptUrl);
		scriptE.src = scriptUrl;
		window.pfnt.windows.scripts.push(scriptE);
		return scriptE;
	}

	function stylesAndScripts() {

		var jsScript = scriptElement(window.pfSiteData.plugin_url + 'Libraries/tinymce/js/tinymce/tinymce.min.js');
		jsScript.onload = function () { console.log("init nt wysiwyg"); window.initEditor(); };
		var headTag = document.getElementsByTagName('head')[0];
		headTag.prepend(jsScript);
		var styleE = document.createElement('style');
		styleE.innerHTML = '#wp-nomthis-editor-container { width: 100%; }' + " \n " +
			'.pressforward-nt__inputfield { width:100%; display:block; }' + " \n " +
			'.pressforward-nt__inner-container { height: 100%; }' + " \n " +
			'.meta-box { background-color: whitesmoke; }' + " \n " +
			'.meta-box img { max-height: 100%; }' + " \n " +
			'.meta-box h5 { font-family: "Arial Black", Gadget, sans-serif; border: 3px #bbbbbb inset; padding: 3px; height: 17%; margin-bottom: 2%; box-sizing: border-box; min-height: 33px; font-size: 14px; }' + " \n " +
			window.pfSiteData.fontFace;
		window.pfnt.windows.styleBlocks.push(styleE);
		headTag.prepend(styleE);
		// headTag.prepend(linkElement('https://github.com/tinymce/tinymce-dist/raw/master/skins/lightgray/fonts/tinymce.ttf', ));
		// <link href=' http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
		// linkElement('https://cdn.quilljs.com/1.0.0/quill.snow.css')
	}

	function ctrlBar() {
		var ctrlContainer = document.createElement('div');
		ctrlContainer.setAttribute('id', 'wp-nomthis-wrap');
		ctrlContainer.setAttribute('class', 'wp-core-ui wp-editor-wrap tmce-active');


		var toolDiv = document.createElement('div');
		toolDiv.setAttribute('id', 'wp-nomthis-editor-tools');
		toolDiv.setAttribute('class', 'wp-editor-tools hide-if-no-js');
		var toolDivInner = document.createElement('div');
		toolDiv.setAttribute('class', 'wp-editor-tabs');
		var visualButton = document.createElement('button');
		visualButton.setAttribute('id', 'nomthis-tmce');
		visualButton.setAttribute('class', 'wp-switch-editor switch-tmce');
		visualButton.setAttribute('data-wp-editor-id', 'nomthis');
		visualButton.innerHTML = 'Visual';
		var textButton = document.createElement('button');
		textButton.setAttribute('id', 'nomthis-tmce');
		textButton.setAttribute('class', 'wp-switch-editor switch-tmce');
		textButton.setAttribute('data-wp-editor-id', 'nomthis');
		textButton.innerHTML = 'Text';

		toolDivInner.appendChild(visualButton);
		toolDivInner.appendChild(textButton);
		toolDiv.appendChild(toolDivInner);

		ctrlContainer.appendChild(toolDiv);

		return ctrlContainer;
	}

	function sidebar(container) {
		var imageStyles = 'background-image: url(' + window.pfMetaData.image + ');' +
			'width: 100%;' +
			'background-size: contain;' +
			'background-repeat: no-repeat;' +
			'background-position: center;' +
			'background-color: transparent;' +
			'height: 77%;' +
			'display: block;' +
			'box-sizing: border-box;';
		var imageArea = generateTag('div', 'pressforward-nt__preview-image', 'preview-image', imageStyles);
		imageArea.src = window.pfMetaData.image;

		var tagStyles = 'width: 80%;' +
			'height: 26px;' +
			'font-size: small;' +
			'margin-left: 10px;';
		var tagContainer = generateTag('div', 'pressforward-nt__preview-tags-container', 'meta-box pressforward-nt-box', 'height:18%; overflow:hidden; display: block;');
		tagContainer.innerHTML = '<h5>Tags</h5><input type="text" value="' + window.pfMetaData.keywords.join(', ') + '" style="' + tagStyles + '">';
		// tagContainer.appendChild(imageArea);

		var imageContainer = generateTag('div', 'pressforward-nt__preview-image-container', 'meta-box pressforward-nt-box', 'height:36%; overflow:hidden; display: block;');
		imageContainer.innerHTML = '<h5>Preview Image</h5>';
		imageContainer.appendChild(imageArea);

		var buttonContainer = generateTag('div', 'pressforward-nt__button-container', 'meta-box pressforward-nt-box', 'height:12%; overflow:hidden; display: block;');
		buttonContainer.innerHTML = '<button id="submit-button" role="presentation" type="button" tabindex="-1" style="width: 100px; height: 30px; margin: 22px 10px; float: right; font-size: 14px;" onclick="window.pfntSubmit(true)">Submit</button>';
		buttonContainer.innerHTML += '<button id="nominate-button" role="presentation" type="button" tabindex="-1" style="width: 100px; height: 30px; margin: 22px 10px; float: right; font-size: 14px;" onclick="window.pfntSubmit(false)">Nominate</button>';
		// buttonContainer.appendChild(imageArea);

		container.appendChild(tagContainer);
		container.appendChild(imageContainer);
		container.appendChild(buttonContainer);
	}

	function pfnt_activate() {

		var pf = window.pfnt;
		var windows = window.pfnt.windows;
		var pf_styles = window.pfnt.styles;
		var pf_tools = window.pfnt.tools;
		var documentClone = document.cloneNode(true);
		window.pfReadability.article = new Readability(documentClone).parse();

		windows.mainWindow = generateTag('div', 'pressforward-nt', 'nomthis-wrapper', '');

		windows.innerWindow = generateTag('div', 'pressforward-nt__inner', 'nomthis-inner-wrapper', '');

		windows.titleField = generateTag('input', 'pressforward-nt__inputfield__title', 'pressforward-nt__inputfield', '');
		windows.titleField.setAttribute('type', 'text');
		if (window.pfReadability.article.title <= 1) {
			windows.titleField.setAttribute('value', window.pfMetaData.title);
		} else {
			windows.titleField.setAttribute('value', window.pfReadability.article.title);
		}

		windows.bylineField = generateTag('input', 'pressforward-nt__inputfield__byline', 'pressforward-nt__inputfield', '');
		windows.bylineField.setAttribute('type', 'text');
		if (window.pfMetaData && window.pfMetaData.hasOwnProperty('author')) {
			windows.bylineField.setAttribute('value', window.pfMetaData.author);
		} else if (window.pfReadability && window.pfReadability.hasOwnProperty('article') && window.pfReadability.article.hasOwnProperty('byline') && window.pfReadability.article.byline.length <= 1) {
			windows.bylineField.setAttribute('value', window.pfReadability.article.byline);
		}

		pf_styles.mwStyles = 'position: fixed;' +
			'width: 76%;' +
			'height: 550px;' +
			'display: block;' +
			'background: #c7c7c7;' +
			'top: 0;' +
			'left: 12%;' +
			'padding: 1px;' +
			'box-sizing: border-box;' +
			'z-index: 9999990000;';

		pf_styles.iwStyles = 'position: relative;' +
			'width: 100%;' +
			'height: 100%;' +
			'background: white;' +
			'border: #afceaf 3px solid;' +
			'box-sizing: border-box;';

		windows.mainWindow.setAttribute('style', pf_styles.mwStyles);

		windows.innerWindow.setAttribute('style', pf_styles.iwStyles);

		windows.innerLeft = generateTag('div', 'pressforward-nt__left', 'pressforward-nt__inner-container', 'float:left; width:60%');
		windows.innerRight = generateTag('div', 'pressforward-nt__right', 'pressforward-nt__inner-container', 'float:right; width:37%; padding-left: 1%; padding: 0 10px;');

		var articleContent = '';
		if (window.pfnt.selection.length > 2) {
			articleContent = window.pfnt.selection;
		} else {
			articleContent = window.pfReadability.article.content;
		}

		var pfMainWindowAppender = function () { document.getElementsByTagName('body')[0].prepend(window.pfnt.windows.mainWindow); };
		var pfInnerWindowAppender = function () {
			window.pfnt.windows.mainWindow.appendChild(window.pfnt.windows.innerWindow);
			window.pfnt.windows.innerWindow.appendChild(window.pfnt.windows.innerLeft);
			window.pfnt.windows.innerWindow.appendChild(window.pfnt.windows.innerRight);
			window.pfnt.windows.innerLeft.appendChild(window.pfnt.windows.titleField);
			window.pfnt.windows.innerLeft.appendChild(window.pfnt.windows.bylineField);
			window.pfnt.windows.innerLeft.appendChild(textareaBuilder(articleContent));
			sidebar(window.pfnt.windows.innerRight);
		};

		pfMainWindowAppender();
		pfInnerWindowAppender();

		window.initEditor = function () {
			window.ntEditor = tinymce.init({ selector: '#nominateText' });
		};

		stylesAndScripts();

	};

	pfnt_activate();

})();

window.pfnt_deactivate = function () {
	function clearInner(node) {
		while (node.hasChildNodes()) {
			clear(node.firstChild);
		}
	}

	function clear(node) {
		while (node.hasChildNodes()) {
			clear(node.firstChild);
		}
		node.parentNode.removeChild(node);
		console.log(node, "cleared!");
	}
	clearInner(window.pfnt.windows.mainWindow);
	window.pfnt.windows.mainWindow.remove();
}

window.pfntSubmit = function (publish) {
	console.log('Submitting to PressForward');
	window.pfnt.submitObject.post_title = window.document.getElementById('pressforward-nt__inputfield__title').value;
	window.pfnt.submitObject.item_author = window.document.getElementById('pressforward-nt__inputfield__byline').value;
	window.pfnt.submitObject.content = tinymce.activeEditor.dom.doc.body.innerHTML;
	window.pfnt.submitObject.item_feat_img = window.pfMetaData.image;
	window.pfnt.submitObject.post_tags = window.document.querySelector('#pressforward-nt__preview-tags-container input').value;
	if (publish) {
		window.pfnt.submitObject.publish = 'Last Step';
		window.pfnt.submitObject.post_status = 'publish';
	}
	var urlEncodedData = "";
	var urlEncodedDataPairs = [];
	Object.keys(window.pfnt.submitObject).forEach((key) => {
		if (window.pfnt.submitObject[key]) {
			if ('string' === typeof window.pfnt.submitObject[key]) {
				var value = window.pfnt.submitObject[key].trim();
			} else {
				var value = window.pfnt.submitObject[key];
			}
			urlEncodedDataPairs.push(encodeURIComponent(key.trim()) + '=' + encodeURIComponent(value));
		}
	});
	urlEncodedData = urlEncodedDataPairs.join('&').replace(/%20/g, '+');
	var xhr = new XMLHttpRequest();
	xhr.open('POST', pfSiteData.submit_endpoint, true);
	//xhr.setRequestHeader("Content-type", "application/json");
	xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xhr.addEventListener('load', function (event) {
		if (xhr.readyState == 4 && xhr.status == 200) {
			var json = JSON.parse(xhr.responseText);
			console.log(json);
		} else {
			console.log(json);
			alert('Nomination failed');
		}
	});
	// var data = JSON.stringify(window.pfnt.submitObject);
	var data = urlEncodedData;
	xhr.send(data);
};
