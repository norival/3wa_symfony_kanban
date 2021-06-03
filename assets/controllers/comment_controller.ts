import { Controller } from 'stimulus';
import { IComment } from '../interfaces/IComment';

export default class extends Controller {
    static targets = ['commentList', 'content'];

    commentListTarget!: HTMLUListElement;
    commentListTargets!: HTMLUListElement[];
    hasCommentListTarget!: boolean;

    contentTarget!: HTMLTextAreaElement;
    contentTargets!: HTMLTextAreaElement[];
    hasContentTarget!: boolean;

    fetchUrl!: string|null;

    initialize() {
        this.fetchUrl = this.element.getAttribute('data-fetch-url');
    }

    connect() {
    }

    saveComment() {
        if (!this.fetchUrl) {
            throw new Error('No url found');
        }

        fetch(this.fetchUrl, {
            method: 'POST',
            body: JSON.stringify({
                content: this.contentTarget.value,
            })
        })
            .then(response => response.json())
            .then(comment => this.renderComment(comment))
    }

    renderComment(comment: IComment) {
        console.log(comment);
        const li = document.createElement('li');
        li.classList.add('comment');
        li.textContent = comment.content;

        const span = document.createElement('span');
        span.classList.add('username');
        span.textContent = comment.user.username;

        li.appendChild(document.createElement('whitespace'));
        li.appendChild(span);
        this.commentListTarget.appendChild(li);
        this.contentTarget.value = '';
    }

    onInputKeydown(event: KeyboardEvent) {
        if (event.key === 'Enter' && event.ctrlKey) {
            this.saveComment();
        }
    }
}
