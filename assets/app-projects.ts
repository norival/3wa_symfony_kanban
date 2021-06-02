import { onDragEnter, onDragLeave, onDragOver, onDragStart, onDrop } from './draggable/tasks';
import './styles/app-projects.scss';

window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.task').forEach(element => {
        element.addEventListener('dragstart', e => {
            onDragStart(e as DragEvent);
        });
    });

    document.querySelectorAll('.dropZone').forEach(e => {
        e.addEventListener('drop', ev => onDrop(ev as DragEvent));
    });

    document.querySelectorAll('.dropZone').forEach(e => {
        e.addEventListener('dragenter', ev => onDragEnter(ev as DragEvent));
    });

    document.querySelectorAll('.dropZone').forEach(e => {
        e.addEventListener('dragleave', ev => onDragLeave(ev as DragEvent));
    });

    document.querySelectorAll('.dropZone').forEach(e => {
        e.addEventListener('dragover', ev => onDragOver(ev as DragEvent));
    });
});
