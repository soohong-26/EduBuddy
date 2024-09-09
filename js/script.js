// Navigation Bar
document.getElementById("navbar").innerHTML = `
    <div class="navbar">
    <image>
        <a href="index.html" class="active">Home</a>
        <a href="about.html">About</a>
        <a href="services.html">Service</a>
        <div class="dropdown">
            <button class="dropbtn">More 
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="contact.html">Contact</a>
                <a href="faq.html">FAQ</a>
            </div>
        </div> 
    </div>
`;