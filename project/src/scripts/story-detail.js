function sdGetStoryId() {
	const body = document.body;
	if (!body || !body.dataset) {
		return 0;
	}

	const storyId = body.dataset.storyId;
	return storyId ? Number(storyId) : 0;
}

function sdPostComment() {
	const textarea = document.getElementById('sd-comment-input');
	const msg = document.getElementById('sd-comment-msg');
	const btn = document.querySelector('.sd-comment-submit');

	if (!textarea || !msg || !btn) {
		return;
	}

	const content = textarea.value.trim();
	if (!content) {
		msg.textContent = 'Bitte zuerst etwas schreiben.';
		msg.style.color = '#c0392b';
		return;
	}

	const storyId = sdGetStoryId();
	if (!storyId) {
		msg.textContent = 'Beitrag konnte nicht geladen werden.';
		msg.style.color = '#c0392b';
		return;
	}

	btn.disabled = true;
	btn.innerHTML = '<i class="fa-regular fa-comment"></i> Posting...';

	fetch('../../src/php/post-comment.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: 'post_id=' + encodeURIComponent(storyId) + '&content=' + encodeURIComponent(content)
	})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				msg.textContent = 'Kommentar gepostet!';
				msg.style.color = 'var(--button-color)';
				textarea.value = '';
				setTimeout(() => location.reload(), 800);
			} else {
				msg.textContent = data.error || 'Etwas ist schiefgelaufen.';
				msg.style.color = '#c0392b';
			}
		})
		.catch(() => {
			msg.textContent = 'Netzwerkfehler, bitte nochmal versuchen.';
			msg.style.color = '#c0392b';
		})
		.finally(() => {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa-regular fa-comment"></i> Post Comment';
		});
}

function sdShowError(message) {
	const errorText = document.getElementById('sd-error-modal-text');
	const errorModal = document.getElementById('sd-error-modal');

	if (!errorText || !errorModal) {
		alert(message || 'Etwas ist schiefgelaufen.');
		return;
	}

	errorText.textContent = message || 'Etwas ist schiefgelaufen.';
	errorModal.classList.add('active');
}

function sdCloseDeleteModal() {
	const deleteModal = document.getElementById('sd-delete-modal');
	if (deleteModal) {
		deleteModal.classList.remove('active');
	}
}

function sdOpenDeleteModal(title, text, onConfirm) {
	const deleteModal = document.getElementById('sd-delete-modal');
	const deleteTitle = document.getElementById('sd-delete-modal-title');
	const deleteText = document.getElementById('sd-delete-modal-text');
	const confirmBtn = document.getElementById('sd-delete-modal-confirm');

	if (!deleteModal || !deleteTitle || !deleteText || !confirmBtn) {
		if (window.confirm(text)) {
			onConfirm();
		}
		return;
	}

	deleteTitle.textContent = title;
	deleteText.textContent = text;

	const newBtn = confirmBtn.cloneNode(true);
	confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

	newBtn.addEventListener('click', () => {
		sdCloseDeleteModal();
		onConfirm();
	});

	deleteModal.classList.add('active');
}

function sdDeleteComment(commentId) {
	sdOpenDeleteModal(
		'Kommentar löschen',
		'Möchtest du diesen Kommentar wirklich löschen?',
		() => {
			fetch('../../src/php/delete-comment.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'comment_id=' + encodeURIComponent(commentId)
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						const comment = document.getElementById('sd-comment-' + commentId);
						if (comment) {
							comment.remove();
						}
					} else {
						sdShowError(data.error || 'Kommentar konnte nicht gelöscht werden.');
					}
				})
				.catch(() => sdShowError('Netzwerkfehler, bitte nochmal versuchen.'));
		}
	);
}

function sdDeletePost(postId) {
	sdOpenDeleteModal(
		'Beitrag löschen',
		'Möchtest du diesen Beitrag wirklich unwiderruflich löschen?',
		() => {
			fetch('../../src/php/delete-post.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: 'post_id=' + encodeURIComponent(postId)
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						window.location.href = '../../index.php#community';
					} else {
						sdShowError(data.error || 'Beitrag konnte nicht gelöscht werden.');
					}
				})
				.catch(() => sdShowError('Netzwerkfehler, bitte nochmal versuchen.'));
		}
	);
}
