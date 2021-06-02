import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    // static targets = ['select'];
    select: HTMLSelectElement|null = null;

    connect() {
        this.select = this.element as HTMLSelectElement;
    }

    change() {
        const fetchUrl = this.select?.dataset.fetchUrl;
        if (!fetchUrl) {
            throw new Error('No url found');
        }

        fetch(fetchUrl, {
            method: 'PATCH',
            body: JSON.stringify({
                status: this.select?.value,
            }),
        })
            .then(response => response.json())
            .then(data => {
                this.updateBoard(data.id, data.status);
            });
    }

    private updateBoard(taskId: string, status: string) {
        if (!this.select) {
            console.error('Element not found');

            return;
        }

        const list = document.querySelector(`.boardElement[data-status="${status}"] .content`)
        const taskElement = document.querySelector(`.task[data-task-id="${taskId}"]`);

        this.select.querySelectorAll('option').forEach(option => {
            if (option.value == status) {
                option.setAttribute('selected', 'true');

                return;
            }

            option.removeAttribute('selected');
        });
        console.log(this.select);

        if (!list || !taskElement) {
            console.error('Element not found');

            return;
        }

        const newElement = taskElement.cloneNode(true);
        list.appendChild(newElement);
        taskElement.remove();
    }
}
