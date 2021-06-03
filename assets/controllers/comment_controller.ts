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

        this.commentListTarget.appendChild(li);
        this.contentTarget.value = '';
    }

    onInputKeydown(event: KeyboardEvent) {
        if (event.key === 'Enter' && event.ctrlKey) {
            this.saveComment();
        }
    }
}
