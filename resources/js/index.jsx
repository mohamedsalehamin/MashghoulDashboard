import {createRoot} from 'react-dom/client';
import App from "./App.jsx";


    document.addEventListener('startSession', () => {
        const container = document.getElementById('agora-react');
        const root = createRoot(container).render(<App/>);
});



document.addEventListener('livewire:init', () => {
    document.addEventListener('sessionClosed', () => {
        Livewire.dispatch('sessionClosed');
    });
});
