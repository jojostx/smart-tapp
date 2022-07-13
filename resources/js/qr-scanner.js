import QrScanner from "qr-scanner";

document.addEventListener("alpine:init", () => {
    Alpine.data("qrcodeScanner", () => {
        return {
            /**
             * @type {(QrScanner|null)} scanner
             */
            scanner: null,
            isScanning: false,
            error: null,
            errorTimerId: null,

            get hasError() {
                return Boolean(this.error);
            },

            async init() {
                this.initScanner();

                this.$watch("isScanning", (n, o) => {
                    this.errorTimerId && clearTimeout(this.errorTimerId);

                    if (n && !o) {
                        this.errorTimerId = setTimeout(() => {
                            this.displayError('Unable to detect QR code, please make sure to focus on the QR code to complete the scan');
                        }, 30000);
                    }
                });
            },

            async initScanner() {
                if (this.scanner && this.scanner?.destroy()) {
                    this.scanner = null;
                }

                if (!(await QrScanner.hasCamera())) {
                    this.displayError(
                        "Qr Code scanning is not supported on your device."
                    );

                    return;
                }

                this.scanner = new QrScanner(
                    this.$refs.qr_scanner,
                    (result) => this.processResult(result),
                    {
                        onDecodeError: (error) => {
                            this.error = error;
                        },
                        highlightScanRegion: true,
                        highlightCodeOutline: true,
                    }
                );

                this.scanner.setInversionMode("both");
            },

            async startScanning() {
                if (Boolean(this.scanner) == false) {
                    return;
                }

                await this.scanner.start();

                this.isScanning = true;
            },

            stopScanning() {
                if (Boolean(this.scanner) == false) {
                    this.isScanning = false;
                    return;
                }

                this.scanner.stop();
                this.isScanning = false;
                this.error = null;
            },

            async toggleFlash() {
                if (Boolean(this.scanner) == false || !this.isScanning) {
                    return;
                }

                if (await this.scanner.hasFlash()) {
                    await this.scanner.toggleFlash();
                }
            },

            processResult: function (result) {
                // do something with the result, eg: pass to lw comp for validation
                console.log(result);
                // stop scanning
                this.stopScanning();
            },

            displayError(error) {
                this.$dispatch("open-alert", {
                    color: "danger",
                    message: error,
                    timeout: 10000,
                });
            },
        };
    });
});
