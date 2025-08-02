<?php
use PHPUnit\Framework\TestCase;

// indexTest.php

class IndexPageTest extends TestCase
{
    public function testSessionStartsBeforeOutput()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status(), 'Session should be active after including index.php');
    }

    public function testHtmlContainsBannerImage()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('<img src="assets/images/west2.png"', $output, 'Banner image should be present in HTML output');
    }

    public function testModalMenuButtonExists()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('id="loginBtn"', $output, 'Menu button should exist in HTML output');
    }

    public function testIncludesAdminLoginModal()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('modals/admin_login_modal.php', $output, 'Admin login modal should be included');
    }

    public function testIncludesFacultyLoginModal()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('modals/faculty_login_modal.php', $output, 'Faculty login modal should be included');
    }

    public function testIncludesRegistrationModal()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('modals/registration_modal.php', $output, 'Registration modal should be included');
    }

    public function testContainsTransitionOpacityStyle()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('transition: opacity 0.2s;', $output, 'CSS should contain transition opacity style');
    }

    public function testPageTitleIsCorrect()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('<title>Cadiz West 2 Elementary School</title>', $output, 'Page title should be correct');
    }

    public function testBootstrapCssIsIncluded()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('bootstrap.min.css', $output, 'Bootstrap CSS should be included');
    }

    public function testSweetAlertScriptIsIncluded()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('cdn.jsdelivr.net/npm/sweetalert2@11', $output, 'SweetAlert2 script should be included');
    }

    public function testBannerCloseButtonExists()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('id="bannerClose"', $output, 'Banner close button should exist');
    }

    public function testBodyHasBackgroundGradient()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%)', $output, 'Body should have background gradient');
    }

    public function testFontIsPoppins()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString("font-family: 'Poppins'", $output, 'Poppins font should be used');
    }

    public function testModalHasCorrectId()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();
        $this->assertStringContainsString('id="myModal"', $output, 'Main modal should have correct id');
    }
}
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('id="loginBtn"', $output, 'Menu button should exist in HTML output');
    }

    public function testIncludesAdminLoginModal()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('modals/admin_login_modal.php', $output, 'Admin login modal should be included');
    }

    public function testIncludesFacultyLoginModal()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('modals/faculty_login_modal.php', $output, 'Faculty login modal should be included');
    }

    public function testIncludesRegistrationModal()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('modals/registration_modal.php', $output, 'Registration modal should be included');
    }

    public function testContainsTransitionOpacityStyle()
    {
        ob_start();
        include 'index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('transition: opacity 0.2s;', $output, 'CSS should contain transition opacity style');
    }
}