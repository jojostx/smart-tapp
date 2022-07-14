import QrScanner from "qr-scanner";

document.addEventListener("alpine:init", () => {
    Alpine.data("qrcodeScanner", (state) => {
        return {
            state,
            /**
             * @type {(QrScanner|null)} scanner
             */
            scanner: null,

            canUseFlash: false,
            
            error: null,

            errorTimerId: null,

            isScanning: false,

            isProcessing: false,

            get hasError() {
                return Boolean(this.error);
            },

            get isFlashOn() {
                return this.scanner?.isFlashOn() ?? false;
            },

            init: async function () {
                this.initScanner();

                this.$watch("isScanning", (n, o) => {
                    this.errorTimerId && clearTimeout(this.errorTimerId);

                    if (n && !o) {
                        this.errorTimerId = setTimeout(() => {
                            this.displayError(
                                "Unable to detect QR code, please make sure to focus on the QR code to complete the scan"
                            );
                        }, 30000);
                    }
                });
            },

            initScanner: async function () {
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

            startScanning: async function () {
                if (Boolean(this.scanner) == false) {
                    return;
                }

                await this.scanner.start();

                this.isScanning = true;
                this.isProcessing = false;
                this.canUseFlash =  await this.scanner.hasFlash();
            },

            stopScanning: function () {
                if (Boolean(this.scanner) == false) {
                    this.isScanning = false;
                    return;
                }

                this.scanner.stop();
                this.isScanning = false;
                this.error = null;
            },

            processResult: function (result) {
                console.log(result);
                // stop scanning
                this.stopScanning();
                // start processing
                this.isProcessing = true;
                // do something with the result, eg: pass to lw comp for validation
                this.state = result;
            },

            toggleFlash: async function () {
                if (this.canUseFlash) {
                    await this.scanner.toggleFlash();
                }
            },

            displayError: function (error) {
                this.$dispatch("open-alert", {
                    color: "danger",
                    message: error,
                    timeout: 10000,
                });
            },
        };
    });
});
