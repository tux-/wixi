'use strict'

const domReady = new Promise((resolve) => {
	if (document.readyState !== 'loading') {
		return resolve();
	}
	else {
		document.addEventListener('DOMContentLoaded', () => {
			return resolve();
		});
	}
});

const insertAtCursor = (node, value) => {
	if ((node.selectionStart) || (node.selectionStart === 0)) {
		let startPos = node.selectionStart;
		let endPos = node.selectionEnd;
		node.value = node.value.substring(0, startPos) + value + node.value.substring(endPos, node.value.length);
		node.selectionStart = startPos + value.length;
		node.selectionEnd = startPos + value.length;
	}
};

const setThemeButton = () => {
	const button = document.querySelector('#toggletheme');
	if ((localStorage.getItem('skin') === 'dark') || ((localStorage.getItem('skin') !== 'light') && (window.matchMedia('(prefers-color-scheme: dark)').matches === true))) {
		button.textContent = 'Dark';
	}
	else {
		button.textContent = 'Light';
	}
};

const autoGrow = function (element) {
	element.style.height = '5px';
	element.style.height = (element.scrollHeight) + 'px';
};

if ((localStorage.getItem('skin') === 'dark') || ((localStorage.getItem('skin') !== 'light') && (window.matchMedia('(prefers-color-scheme: dark)').matches === true))) {
	document.querySelector('html').classList.add('dark');
}

domReady.then(() => {
	if (document.querySelector('#editorjs') !== null) {

		const editor = new EditorJS({
			tools: {
				header: {
					class: Header,
					inlineToolbar: true,
				},
				list: {
					class: NestedList,
					inlineToolbar: true,
				},
				code: {
					class: CodeTool,
					inlineToolbar: true,
				},
				inlineCode: {
					class: InlineCode,
					inlineToolbar: true,
				},
				table: {
					class: Table,
					inlineToolbar: true,
				},
				underline: {
					class: Underline,
					inlineToolbar: true,
				},
				delimiter: {
					class: Delimiter,
					inlineToolbar: true,
				},
				marker: {
					class: Marker,
					inlineToolbar: true,
				},
				checklist: {
					class: Checklist,
					inlineToolbar: true,
				},
				alert: {
					class: Alert,
					inlineToolbar: true,
					config: {
						alertTypes: ['info', 'success', 'warning', 'danger'],
						defaultType: 'info',
					},
				},
				strikethrough: {
					class: Strikethrough,
					inlineToolbar: true,
				},
				inlineCode: InlineCode,
			},
			onReady: () => {
				fetch(gimle.BASE_PATH + 'contents?page=' + encodeURIComponent(document.location.href.substring(gimle.BASE_PATH.length))).then(r => {
					return r.json();
				}).then(r => {
					if (r === false) {
						document.querySelector('#editorjs').innerHTML = `<div class="error">
							<h1>Error</h1>
							<p>Check that you have no illegal characters in the url.</p>
							<p>Url can not contain . : ; \\ " ' < > | ? # or doubble slash //</p>
						</div>`;
					}
					else {
						editor.render(r).then(() => {
							document.querySelectorAll('textarea').forEach((elm) => {
								autoGrow(elm);
								elm.setAttribute('spellcheck', 'false');
							});
						});
					}
				});
			},
			onChange: (config, evts) => {
				for (let evt in evts) {
					if (typeof(evts[evt]) !== 'object') {
						continue;
					}
					if (evts[evt] === null) {
						continue;
					}
					if (evts[evt].type !== 'block-added') {
						continue;
					}
					document.querySelectorAll('textarea').forEach((elm) => {
						autoGrow(elm);
						elm.setAttribute('spellcheck', 'false');
					});
				}
			}
		});

		gimle(window).on('click', (evt) => {
			if (evt.target.closest('[data-editorjs="save"]')) {
				evt.preventDefault();
				const target = evt.target.closest('[data-editorjs="save"]');
				editor.save().then(data => {
					fetch(gimle.BASE_PATH + 'save?page=' + encodeURIComponent(document.location.href.substring(gimle.BASE_PATH.length)), {
						method: 'post',
						body: JSON.stringify(data),
					}).then(r => {
						return r.json();
					}).then(r => {
						if (r === true) {
							target.classList.add('saved');
							setTimeout(() => {
								target.classList.remove('saved');
							}, 200);
						}
						else {
							alert('There was an error saving, check spectacle');
							target.classList.add('error');
							setTimeout(() => {
								target.classList.remove('error');
							}, 200);
						}
					});
				});
				return false;
			}
		});

		gimle('[data-action="settings"]').on('click', (evt) => {
			evt.preventDefault();
			document.querySelector('#postmenu').showModal();
			return false;
		});
		gimle('dialog .close').on('click', (evt) => {
			evt.preventDefault();
			document.querySelector('#postmenu').close();
			return false;
		});

		gimle(document).on('input', 'textarea', (evt) => {
			autoGrow(evt.target);
		});

		gimle('#movepost').on('submit', (evt) => {
			evt.preventDefault();
			const data = new URLSearchParams();
			for (const pair of new FormData(evt.target)) {
				data.append(pair[0], pair[1]);
			}
			fetch(evt.target.action, {
				method: evt.target.method,
				body: data,
			}).then(r => {
				return r.json();
			}).then(r => {
				if (r.error !== undefined) {
					alert(r.error);
					return;
				}
				document.location.href = gimle.BASE_PATH + 'wiki/' + r.target;
			});
			return false;
		});

		gimle(window).on('keydown', (evt) => {
			if (evt.key === 'Tab') {
				if (event.target.nodeName.toLowerCase() === 'textarea') {
					evt.preventDefault();
					evt.stopPropagation();
					if (evt.shiftKey === false) {
						insertAtCursor(document.querySelector('textarea'), "\t");
					}
					return false;
				}
			}
			if ((evt.key === 's') && (evt.ctrlKey === true)) {
				evt.preventDefault();
				evt.stopPropagation();
				document.querySelector('header button').click();
				return false;
			}
		}, true);
		gimle(window).on('click', 'a', (evt) => {
			if ((evt.shiftKey === true) || (evt.ctrlKey === true) || (evt.metaKey === true)) {
				evt.preventDefault();
				evt.stopPropagation();
				let href = evt.target.href;
				if (href.startsWith('base://')) {
					href = gimle.BASE_PATH + href.substring(7);
				}
				else if (href.startsWith('wiki://')) {
					href = gimle.BASE_PATH + 'wiki/' + href.substring(7);
				}
				if ((evt.shiftKey === false) && (evt.ctrlKey === false) && (evt.metaKey === true)) {
					document.location.href = href;
				}
				else {
					window.open(href, '_blank');
				}
				return false;
			}
		}, true);
		gimle(window).on('paste', (evt) => {
			if ((evt.target.nodeName.toLowerCase() === 'input') && (evt.target.classList.contains('ce-inline-tool-input') === true)) {
				evt.preventDefault();
				evt.stopPropagation();
				var items = evt.clipboardData.items;
				if (items.length === 0) {
					console.log('No clipboard data detected');
					return false;
				}
				for (let item of items) {
					if ((item.kind === 'string') && (item.type === 'text/plain')) {
						item.getAsString((r) => {
							if (r.startsWith(gimle.BASE_PATH + 'wiki/')) {
								r = 'wiki://' + r.substring(gimle.BASE_PATH.length + 5);
							}
							else if (r.startsWith(gimle.BASE_PATH)) {
								r = 'base://' + r.substring(gimle.BASE_PATH.length);
							}
							insertAtCursor(evt.target, r);
						});
					}
				}
				return false;
			}
		}, true);
	}

	gimle('[data-action="menu"]').on('click', (evt) => {
		evt.preventDefault();
		const menu = document.querySelector('#menu');
		if (menu.classList.contains('visible')) {
			menu.classList.remove('visible');
			return false;
		}

		setThemeButton();

		menu.classList.add('visible');
		return false;
	});
	gimle('#toggletheme').on('click', (evt) => {
		evt.preventDefault();
		const htmlClass = document.querySelector('head').classList;
		if (localStorage.getItem('skin') !== null) {
			localStorage.removeItem('skin');
			document.querySelector('html').classList.remove('dark', 'light');
		}
		else if (window.matchMedia('(prefers-color-scheme: dark)').matches === true) {
			localStorage.setItem('skin', 'light');
			document.querySelector('html').classList.add('light');
		}
		else {
			localStorage.setItem('skin', 'dark');
			document.querySelector('html').classList.add('dark');
		}

		setThemeButton();
	});
});
