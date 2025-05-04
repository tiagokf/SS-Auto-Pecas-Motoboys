        </div> <!-- Fim do segment -->
        </div> <!-- Fim do container -->

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Semantic UI JS -->
        <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
        <!-- JS personalizado -->
        <script src="assets/js/script.js"></script>

        <script>
            $(document).ready(function () {
                // Inicializar elementos do Semantic UI
                $('.ui.dropdown').dropdown();
                $('.ui.modal').modal();

                // Mensagens de alerta com tempo
                $('.message .close').on('click', function () {
                    $(this).closest('.message').transition('fade');
                });

                // Auto-hide para mensagens após 5 segundos
                setTimeout(function () {
                    $('.message').transition('fade');
                }, 5000);
            });
        </script>
        </body>

        </html>