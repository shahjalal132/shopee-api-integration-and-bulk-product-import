(function ($) {
  $(document).ready(function () {
    // show notification
    function showNotification(message) {
      Toastify({
        text: message,
        duration: 3000,
        newWindow: true,
        close: true,
        gravity: "top", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
          background: "linear-gradient(to right, #00b09b, #96c93d)",
        },
        onClick: function () {}, // Callback after click
      }).showToast();
    }

    // handle save credentials
    $("#credential-save").on("click", function (e) {
      e.preventDefault();

      // get credentials from form
      let client_id = $("#client-id").val();
      let client_secret = $("#client-secret").val();

      // make ajax call to save credentials
      $.ajax({
        url: bulkProductImport.ajax_url,
        method: "POST",
        data: {
          action: "save_client_credentials",
          nonce: bulkProductImport.nonce,
          client_id: client_id,
          client_secret: client_secret,
        },
        success: function (response) {
          if (response.success) {
            let successMessage = response.data;
            // Display an info toast with no title
            showNotification(successMessage);
          } else {
            let errorMessage = response.data;
          }
        },
        error: function () {
          alert("An error occurred. Please try again.");
        },
      });
    });

    // handle db tables creation
    $("#save-table-prefix").on("click", function (e) {
      e.preventDefault();

      let tablePrefix = $("#table-prefix").val();

      $.ajax({
        type: "POST",
        url: bulkProductImport.ajax_url,
        data: {
          action: "save_table_prefix",
          nonce: bulkProductImport.nonce,
          table_prefix: tablePrefix,
        },
        success: function (response) {
          let successMessage = response.data;
          // Display an info toast with no title
          showNotification(successMessage);
        },
      });
    });

    // handle db tables creation
    $("#shopee-credential-save").on("click", function (e) {
      e.preventDefault();

      let shopee_base_url = $("#shopee_base_url").val();
      let shopee_partner_id = $("#shopee_partner_id").val();
      let shopee_partner_key = $("#shopee_partner_key").val();
      let shopee_shop_id = $("#shopee_shop_id").val();
      let shopee_access_token = $("#shopee_access_token").val();
      let shopee_redirect_url = $("#shopee_redirect_url").val();
      let shopee_auth_code = $("#shopee_auth_code").val();

      $.ajax({
        type: "POST",
        url: bulkProductImport.ajax_url,
        data: {
          action: "save_shopee_credentials",
          nonce: bulkProductImport.nonce,
          shopee_base_url: shopee_base_url,
          shopee_partner_id: shopee_partner_id,
          shopee_partner_key: shopee_partner_key,
          shopee_shop_id: shopee_shop_id,
          shopee_access_token: shopee_access_token,
          shopee_redirect_url: shopee_redirect_url,
          shopee_auth_code: shopee_auth_code,
        },
        success: function (response) {
          let successMessage = response.data;
          // Display an info toast with no title
          showNotification(successMessage);
        },
      });
    });

    // tabs
    $("#tabs").tabs();

    // Authentication
    $("#shopee-shop-authentication").click(function (e) {
      e.preventDefault();

      let shopee_loader_wrapper = $(".shopee-auth-wrapper");
      // add loader class
      shopee_loader_wrapper.addClass("loader");

      $.ajax({
        type: "POST",
        url: bulkProductImport.ajax_url,
        data: {
          action: "shopee_shop_authentication",
          nonce: bulkProductImport.nonce,
        },
        success: function (response) {
          // remove loader class
          shopee_loader_wrapper.removeClass("loader");

          let auth_url = response.data;
          if (auth_url) {
            window.location.href = auth_url;
          }

          const successMessage =
            "Generate Shopee Authentication URL Go to Url and Authenticate";
          showNotification(successMessage);
        },
      });
    });

    function initializeConfetti(buttonId) {
      let button = document.getElementById(buttonId);
      let confetti = new Confetti(buttonId);
      confetti.setCount(75);
      confetti.setSize(1);
      confetti.setPower(25);
      confetti.setFade(false);
      confetti.destroyTarget(false);

      button.addEventListener("click", function () {
        confetti.shoot();
      });
    }
    // Initialize confetti for each button
    initializeConfetti("credential-save");
    initializeConfetti("save-table-prefix");

    // copy to clipboard
    function copyToClipboard(text) {
      let tempInput = document.createElement("input");
      tempInput.style.position = "absolute";
      tempInput.style.left = "-9999px";
      tempInput.value = text;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
    }

    document.getElementById("status-cp").addEventListener("click", function () {
      let statusApi = document.getElementById("status-api").textContent;
      copyToClipboard(statusApi);
      showNotification("Copied to clipboard!");
    });

    document.getElementById("delete-cp").addEventListener("click", function () {
      let deleteApi = document.getElementById("delete-api").textContent;
      copyToClipboard(deleteApi);
      showNotification("Copied to clipboard!");
    });

    document
      .getElementById("delete-trash-cp")
      .addEventListener("click", function () {
        let deleteTrashApi =
          document.getElementById("delete-trash-api").textContent;
        copyToClipboard(deleteTrashApi);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("delete-woo-cats-cp")
      .addEventListener("click", function () {
        let deleteWooCats = document.getElementById(
          "delete-woo-cats-api"
        ).textContent;
        copyToClipboard(deleteWooCats);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("sync-products-cp")
      .addEventListener("click", function () {
        let syncProducts =
          document.getElementById("sync-products-api").textContent;
        copyToClipboard(syncProducts);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("insert-products-cp")
      .addEventListener("click", function () {
        let insertProductsDb = document.getElementById(
          "insert-products-api"
        ).textContent;
        copyToClipboard(insertProductsDb);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("insert-price-cp")
      .addEventListener("click", function () {
        let syncProducts =
          document.getElementById("insert-price-api").textContent;
        copyToClipboard(syncProducts);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("insert-stock-cp")
      .addEventListener("click", function () {
        let syncProducts =
          document.getElementById("insert-stock-api").textContent;
        copyToClipboard(syncProducts);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("sign_generate_cp")
      .addEventListener("click", function () {
        let signGenerate =
          document.getElementById("sign-generate-api").textContent;
        copyToClipboard(signGenerate);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("insert_categories_cp")
      .addEventListener("click", function () {
        let insert_category_db =
          document.getElementById("insert_category").textContent;
        copyToClipboard(insert_category_db);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("insert_order_list_cp")
      .addEventListener("click", function () {
        let insert_order_list =
          document.getElementById("insert_order_list").textContent;
        copyToClipboard(insert_order_list);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("insert_order_details_cp")
      .addEventListener("click", function () {
        let insert_order_details = document.getElementById(
          "insert_order_details"
        ).textContent;
        copyToClipboard(insert_order_details);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("sync_orders_cp")
      .addEventListener("click", function () {
        let sync_orders = document.getElementById("sync_orders").textContent;
        copyToClipboard(sync_orders);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("get_access_token_cp")
      .addEventListener("click", function () {
        let get_access_token =
          document.getElementById("get_access_token").textContent;
        copyToClipboard(get_access_token);
        showNotification("Copied to clipboard!");
      });

    document
      .getElementById("refresh_access_token_cp")
      .addEventListener("click", function () {
        let refresh_access_token = document.getElementById(
          "refresh_access_token"
        ).textContent;
        copyToClipboard(refresh_access_token);
        showNotification("Copied to clipboard!");
      });
  });
})(jQuery);
