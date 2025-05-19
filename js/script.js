document.addEventListener('DOMContentLoaded', function () {
    let dernierChargement = '2000-01-01T00:00:00';
    const calendarEl = document.getElementById('calendar');



    console.log("Dernier chargement : " + dernierChargement);
    // Definition de la freaquence du polling
    const pollingInterval = 30 * 1000; // 30 secondes

    //Excecution de la fonction nouveauNotification() toutes les 30s
    nouveauNotification();
    setInterval(() => {
        nouveauNotification();
    }, pollingInterval);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr', // Pour afficher le calendrier en français
        events: 'php/eventsDB.php',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        eventTimeFormat: { // Format de l'heure
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    });
    calendar.render();

    async function nouveauNotification() {
        try {
            const reponse = await fetch(`php/nouveauNotification.php?since=${encodeURIComponent(dernierChargement)}`);
            if (!reponse.ok) {
                throw new Error("Une erreur s'est produit lors du chargement des donnéés");
            }
            const data = await reponse.json();
            console.log(data);

            //Mettre a jour le temps du dernier chargement si ça reussi
            dernierChargement = new Date().toISOString();

            //Traitement des nouveaux données

            if (data.new && data.new.length > 0) {
                afficherNotification(data.new);
                calendar.refetchEvents();
            }
        } catch (err) {
            console.error('Erreur : ', err)
        }

    }

    function afficherNotification(nouveauNotif) {
        // Récupère bien le <ul>
        const list = document.getElementById('notif-list');

        // Pour chaque notification...
        nouveauNotif.forEach(notif => {
            const li = document.createElement('li');

            const date = new Date(notif.date_rdv);
            const formatted = date.toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            li.textContent = `🔔 ${formatted} — ${notif.patient} type : ${notif.rdvtype}`;

            // Insère en tête de liste
            list.prepend(li);
        });
    }

});