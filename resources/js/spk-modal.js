// document.addEventListener('DOMContentLoaded', function() {
//     // Simple approach: directly show the appropriate modal based on data-action
//     document.querySelectorAll('.btn-modal-trigger').forEach(button => {
//         button.addEventListener('click', function() {
//             const action = this.getAttribute('data-action');
            
//             if (action === 'close_order') {
//                 // Show the close order modal
//                 const closeModalElement = document.getElementById('closeOrderModal');
//                 if (closeModalElement) {
//                     const closeModal = new bootstrap.Modal(closeModalElement);
//                     closeModal.show();
//                 }
//             } else if (action === 'reject_order') {
//                 // Show the reject order modal
//                 const rejectModalElement = document.getElementById('rejectOrderModal');
//                 if (rejectModalElement) {
//                     const rejectModal = new bootstrap.Modal(rejectModalElement);
//                     rejectModal.show();
//                 }
//             }
//         });
//     });
// });