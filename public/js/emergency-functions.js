// Script de emergência para garantir que removerImagem funcione
// Função de emergência para remover imagem (JavaScript puro)
if (typeof window.removerImagem === 'undefined') {
    window.removerImagem = function(imagemId) {
        console.log('Função de emergência - removerImagem ID:', imagemId);
        
        if (confirm('Tem certeza que deseja remover esta imagem? Esta ação não pode ser desfeita.')) {
            // Criar form dinamicamente
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/comerciantes/produtos/2/imagens/' + imagemId;
            
            // Token CSRF
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            var metaToken = document.querySelector('meta[name="csrf-token"]');
            csrfInput.value = metaToken ? metaToken.getAttribute('content') : '';
            
            // Method DELETE
            var methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            // Adicionar inputs ao form
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            
            // Adicionar form ao body e submeter
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    console.log('Função de emergência definida para removerImagem');
}

// Função de emergência para setPrincipal
if (typeof window.setPrincipal === 'undefined') {
    window.setPrincipal = function(imagemId) {
        console.log('Função de emergência - setPrincipal ID:', imagemId);
        
        if (confirm('Deseja definir esta imagem como principal?')) {
            window.location.href = '/comerciantes/produtos/2/imagens/' + imagemId + '/set-principal';
        }
    };
    
    console.log('Função de emergência definida para setPrincipal');
}

// Verificar se as funções estão disponíveis
console.log('Status das funções:');
console.log('- removerImagem:', typeof window.removerImagem);
console.log('- setPrincipal:', typeof window.setPrincipal);
