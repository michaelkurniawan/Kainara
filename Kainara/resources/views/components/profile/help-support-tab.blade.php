<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manual Accordion FAQ</title>
  <style>
    body {
      font-family: serif;
      overflow-x: hidden; /* hilangkan horizontal */
      overflow-y: auto;   /* biarkan vertikal */
    }

    .accordion-item {
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-bottom: 8px;
    }

    .accordion-header {
      padding: 15px;
      cursor: pointer;
      user-select: none;
      position: relative;
      background-color: white;
      color: black;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Header berwarna merah saat aktif */
    .accordion-item.active .accordion-header {
      background-color: #AD9D6D;
      color: white;
    }

    .accordion-header .arrow {
      position: absolute;
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px;
      height: 16px;
      transition: transform 0.3s ease;
    }

    .accordion-header .arrow img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.3s ease;
    }

    .accordion-item.active .accordion-header .arrow img {
      transform: rotate(180deg);
    }

    .accordion-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease, padding 0.3s ease;
      padding: 0 15px;
    }

    .accordion-item.active .accordion-content {
      padding: 15px;
      max-height: 500px;
    }
  </style>
</head>
<body>

<!-- Konten FAQ -->
<div class="tab-pane" id="help-support" role="tabpanel" aria-labelledby="help-support-tab">
  <div class="addresses-header d-flex justify-content-between align-items-center mb-4">
    <h3 class="personal-info-title mb-0 font-serif-medium">Help & Support</h3>
  </div>
  <hr class="mb-4">
  <div class="faq-section text-left">
    <h4>Frequently Asked Questions (FAQ)</h4>
    <div class="accordion">

      <!-- Contoh FAQ -->
      <div class="accordion-item">
        <div class="accordion-header">
          How do I change my personal information (name, date of birth)?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          You can change your first name and last name in the "Personal Information" tab on your profile page. Click the pencil icon next to "Private Info" to edit. For date of birth, if it's not directly editable, please contact our support team.
        </div>
      </div>

      <!-- Salin struktur ini untuk semua item lainnya -->
      <div class="accordion-item">
        <div class="accordion-header">
          How do I change my profile picture?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          Hover over your profile picture at the top of your profile page. A pencil icon will appear. Click the icon to upload a new image.
        </div>
      </div>
    
      <div class="accordion-item">
        <div class="accordion-header">
          I forgot my password, how do I reset it?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          You can use the "Forgot Password" feature on the login page. Follow the instructions sent to your registered email.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          How do I add a new address?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          In the "Addresses" tab, click the "+ Add new address" button. Fill in the address details in the modal that appears and save.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          How do I edit an existing address?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          In the "Addresses" tab, find the address you want to edit, then click the "Edit" button next to it. A modal will appear with the pre-filled address details; you can modify them and save the changes.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          How do I delete an address?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          In the "Addresses" tab, find the address you want to delete, then click the "Delete" button next to it. You will be asked for confirmation before the address is permanently deleted.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          How do I set a default address?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          When adding or editing an address, there is an option "Set as Default Address". Check this box to make this your primary address. Only one address can be set as default.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          How do I view my order history?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          All your past orders can be viewed in the "Order History" tab. You will see details such as order ID, number of items, total price, shipping address, and order status.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          What do the different order statuses mean?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          <ul>
            <li><strong>Completed:</strong> The order has been successfully processed and delivered.</li>
            <li><strong>Waiting for Payment:</strong> Your order has been placed but payment has not yet been confirmed.</li>
            <li><strong>Cancelled:</strong> The order has been cancelled.</li>
          </ul>
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          I have an issue with my order, what should I do?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          If you have an issue with an order, please note your order ID and contact our support team using the information in this "Help & Support" tab.
        </div>
      </div>

      <div class="accordion-item">
        <div class="accordion-header">
          How do I contact the support team?
          <span class="arrow"><img src="/images/icons/arrow-down.png" alt="Arrow" /></span>
        </div>
        <div class="accordion-content">
          You can find our support team's contact information on this "Help & Support" tab. We are ready to assist you with any questions or issues.
        </div>
      </div>

    </div>

    </div>
    <p class="mt-4">If your question is not answered here, feel free to contact us!</p>
  </div>
</div>

<script>
  const headers = document.querySelectorAll('.accordion-header');

  headers.forEach(header => {
    header.addEventListener('click', () => {
      const item = header.parentElement;
      const isActive = item.classList.contains('active');

      document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));

      if (!isActive) {
        item.classList.add('active');
      }
    });
  });
</script>

</body>
</html>