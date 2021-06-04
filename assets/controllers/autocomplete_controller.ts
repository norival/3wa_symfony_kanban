import { Controller } from 'stimulus';

interface IUser {
    id: string;
    firstname: string;
    name: string;
    nickname: string;
    profilePicture?: string;
}

export default class extends Controller {
    static values = {
        list: String,
    };

    static targets = [
        'input',
        'suggestions',
        'suggestionsContainer',
        'renderSelection',
    ];

    inputTarget!: HTMLInputElement;
    inputTargets!: HTMLInputElement[];
    hasInputTarget!: boolean;

    suggestionsTarget!: HTMLInputElement;
    suggestionsTargets!: HTMLInputElement[];
    hasSuggestionsTarget!: boolean;

    suggestionsContainerTarget!: HTMLInputElement;
    suggestionsContainerTargets!: HTMLInputElement[];
    hasSuggestionsContainerTarget!: boolean;

    renderSelectionTarget!: HTMLInputElement;
    renderSelectionTargets!: HTMLInputElement[];
    hasRenderSelectionTarget!: boolean;

    fetchUrl!: string|null;
    select: HTMLSelectElement|null = null;
    listValue!: String;

    selectedIndex = 0;

    initialize() {
        this.fetchUrl = this.element.getAttribute('data-fetch-url');

        const selectId = this.element.getAttribute('data-select-id');
        if (selectId) {
            this.select = document.getElementById(selectId) as HTMLSelectElement|null;
            this.select?.closest('.form-group')?.classList.add('hide');
        }
    }

    connect() {
    }

    input() {
        if (!this.fetchUrl) {
            throw new Error('No url found');
        }

        if (this.inputTarget.value === '') {
            this.renderSuggestions([]);
            return;
        }

        if (this.inputTarget.value.length < 3) {
            return;
        }

        fetch(`${this.fetchUrl}?q=${this.inputTarget.value}`, {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => this.renderSuggestions(data));
    }

    renderSuggestions(users: IUser[]): void {
        this.suggestionsContainerTarget.classList.remove('hide');
        this.suggestionsTarget.innerHTML = '';
        users.forEach((user, index) => {
            const li = document.createElement('li');
            const a = document.createElement('a');

            if (index === 0) {
                li.classList.add('selected');
                this.selectedIndex = 0;
            }

            li.dataset.index = `${index}`;

            a.setAttribute('href', '#');
            a.dataset.userId = user.id;
            a.dataset.userDisplayName = `${user.firstname} ${user.name}`;
            a.innerHTML = '';

            if (user.profilePicture) {
                a.innerHTML += `
                    <img src="/uploads/profile-pictures/${user.profilePicture}" alt="...">
                `;
            }

            a.innerHTML += `
                <span>${user.firstname} ${user.name}</span>
            `;

            li.appendChild(a);
            this.suggestionsTarget.append(li);
        });
    }

    selectElement(event: MouseEvent) {
        let target = event.target as HTMLAnchorElement;
        if (target.tagName !== 'A') {
            const a = target.closest('a');
            if (a) {
                target = a;
            }
        }

        this.select?.querySelectorAll('option').forEach(option => {
            if (option.value === target.dataset.userId) {
                option.setAttribute('selected', 'selected');
            }
        });

        let li = this.renderSelectionTarget.querySelector(`li[data-user-id='${target.dataset.userId}']`);

        if (!li) {
            li = document.createElement('li');
            li.classList.add('userPreview');
            li.innerHTML = `
                <img src="uploads/profile-pictures/PROFILE_PICTURE" class="profilePicture">
                <span class="userName">${target.dataset.userDisplayName}</span>
                <button class="removeUser"
                        data-user-id="${target.dataset.userId}"
                        data-action="autocomplete#removeElement">
                    <i class="fas fa-times"></i>
                </button>
            `;
            this.renderSelectionTarget.appendChild(li);
        }

        this.suggestionsTarget.innerHTML = '';
        this.inputTarget.value = '';
    }

    sendElement(event: MouseEvent) {
        const sendUrl = this.suggestionsTarget.dataset.sendUrl;

        let target = event.target as HTMLAnchorElement;
        if (target.tagName !== 'A') {
            const a = target.closest('a');
            if (a) {
                target = a;
            }
        }

        if (sendUrl) {
            fetch(sendUrl, {
                method: 'PATCH',
                body: JSON.stringify({
                    user: target.dataset.userId,
                }),
            })
                .then(response => response.json())
                .then((user: IUser) => {
                    const li = document.createElement('li');
                    li.classList.add('assignee');

                    li.dataset.userId = user.id;
                    li.innerText = user.nickname;

                    const list = document.querySelector(`${this.listValue}`);

                    list?.appendChild(li);

                    this.suggestionsTarget.innerHTML = '';
                })
        }
    }

    inputFocus() {
        this.suggestionsContainerTarget.classList.remove('hide');
    }

    inputFocusOut() {
        setTimeout(() => {
            this.suggestionsContainerTarget.classList.add('hide');
        }, 500);
    }

    inputKeydown(event: KeyboardEvent) {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {

            const suggestionsLength = this.suggestionsTarget.querySelectorAll('li').length;
            if (event.key === 'ArrowDown') {
                this.selectedIndex++;
                if (this.selectedIndex >= suggestionsLength) {
                    this.selectedIndex = 0;
                }
            } else {
                this.selectedIndex--;
                if (this.selectedIndex < 0) {
                    this.selectedIndex = suggestionsLength - 1;
                }
            }

            this.suggestionsTarget.querySelector('li.selected')?.classList.remove('selected');

            this.suggestionsTarget
                .querySelector(`[data-index='${this.selectedIndex}']`)
                ?.classList.add('selected');
        }

        if (event.key === 'Enter') {
            const a = this.suggestionsTarget.querySelector(`[data-index='${this.selectedIndex}'] a`);

            a?.dispatchEvent(new Event('click', {bubbles: true}));
        }
    }

    removeElement(event: MouseEvent) {
        let target = event?.target as HTMLButtonElement;

        if (target.tagName !== 'BUTTON') {
            const button = target.closest('button');
            if (button) {
                target = button;
            }
        }
        this.select?.querySelectorAll('option').forEach(option => {
            if (option.value === target.dataset.userId) {
                option.removeAttribute('selected');
            }
        });

        target.closest('.userPreview')?.remove();
    }
}
