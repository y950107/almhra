<x-filament::layouts.app>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabContainer = document.querySelector('[data-tabs-id="settings-tabs"]'); 
            if (!tabContainer) return;


            const activeTab = localStorage.getItem("activeSettingsTab");
            if (activeTab) {
                const tabButton = tabContainer.querySelector(`[aria-controls="${activeTab}"]`);
                if (tabButton) tabButton.click();
            }


            tabContainer.querySelectorAll("[role='tab']").forEach(tab => {
                tab.addEventListener("click", function () {
                    localStorage.setItem("activeSettingsTab", this.getAttribute("aria-controls"));
                });
            });
        });
    </script>
</x-filament::layouts.app>